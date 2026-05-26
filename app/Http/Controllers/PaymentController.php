<?php

namespace App\Http\Controllers;

use App\Models\StudioSession;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    public function checkout(Request $request, StudioSession $studioSession): RedirectResponse
    {
        $this->authorize('pay', $studioSession);

        $studioSession->load(['studio', 'booker']);

        Stripe::setApiKey(config('services.stripe.secret'));

        $checkoutSession = Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'customer_email' => $request->user()->email,
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'mxn',
                        'unit_amount' => (int) round((float) $studioSession->total_price * 100),
                        'product_data' => [
                            'name' => 'Reserva: '.$studioSession->title,
                            'description' => 'Estudio: '.$studioSession->studio->name,
                        ],
                    ],
                    'quantity' => 1,
                ],
            ],
            'metadata' => [
                'studio_session_id' => (string) $studioSession->id,
                'user_id' => (string) $request->user()->id,
            ],
            'success_url' => route('payments.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('studio-sessions.show', $studioSession),
        ]);

        $studioSession->forceFill([
            'payment_status' => 'pending',
            'stripe_checkout_session_id' => $checkoutSession->id,
        ])->save();

        return redirect()->away($checkoutSession->url);
    }

    public function success(Request $request): View|RedirectResponse
    {
        $sessionId = $request->query('session_id');

        if (! is_string($sessionId)) {
            return redirect()
                ->route('studios.index')
                ->with('status', 'No se encontró la sesión de pago.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $checkoutSession = Session::retrieve($sessionId);

        $studioSessionId = $checkoutSession->metadata->studio_session_id ?? null;

        if (! $studioSessionId) {
            return redirect()
                ->route('studios.index')
                ->with('status', 'No se encontró la reserva asociada al pago.');
        }

        $studioSession = StudioSession::query()
            ->with(['studio.owner', 'booker', 'musicians'])
            ->findOrFail($studioSessionId);

        if ($checkoutSession->payment_status === 'paid') {
            $this->markSessionAsPaid($studioSession, $checkoutSession);
        }

        return view('payments.success', [
            'studioSession' => $studioSession->fresh(['studio.owner', 'booker', 'musicians']),
        ]);
    }

    public function webhook(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                $webhookSecret
            );
        } catch (UnexpectedValueException|SignatureVerificationException) {
            return response('Invalid webhook signature.', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $checkoutSession = $event->data->object;

            $studioSessionId = $checkoutSession->metadata->studio_session_id ?? null;

            if ($studioSessionId) {
                $studioSession = StudioSession::query()->find($studioSessionId);

                if ($studioSession instanceof StudioSession) {
                    $this->markSessionAsPaid($studioSession, $checkoutSession);
                }
            }
        }

        return response('Webhook handled.', 200);
    }

    private function markSessionAsPaid(StudioSession $studioSession, object $checkoutSession): void
    {
        if ($studioSession->payment_status === 'paid') {
            return;
        }

        $amountPaid = isset($checkoutSession->amount_total)
            ? ((int) $checkoutSession->amount_total) / 100
            : (float) $studioSession->total_price;

        $studioSession->forceFill([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'stripe_checkout_session_id' => $checkoutSession->id,
            'stripe_payment_intent_id' => $checkoutSession->payment_intent ?? null,
            'amount_paid' => $amountPaid,
            'paid_at' => now(),
        ])->save();
    }
}
