<?php

use App\Http\Controllers\PaymentController;
use App\Models\Studio;
use App\Models\StudioSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

function signedStripeRequest(string $payload, string $secret): Request
{
    $timestamp = time();

    $signedPayload = $timestamp.'.'.$payload;

    $signature = hash_hmac(
        algo: 'sha256',
        data: $signedPayload,
        key: $secret
    );

    $stripeSignatureHeader = "t={$timestamp},v1={$signature}";

    return Request::create(
        uri: '/stripe/webhook',
        method: 'POST',
        parameters: [],
        cookies: [],
        files: [],
        server: [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => $stripeSignatureHeader,
        ],
        content: $payload
    );
}

test('stripe webhook marks studio session as paid', function () {
    config([
        'services.stripe.webhook_secret' => 'whsec_test_secret',
    ]);

    $producer = User::factory()->producer()->create();
    $musician = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
    ]);

    $studioSession = StudioSession::factory()->confirmed()->create([
        'studio_id' => $studio->id,
        'booked_by' => $musician->id,
        'payment_status' => 'pending',
        'total_price' => 1000,
        'stripe_checkout_session_id' => 'cs_test_123',
        'stripe_payment_intent_id' => null,
        'amount_paid' => null,
        'paid_at' => null,
    ]);

    $payload = json_encode([
        'id' => 'evt_test_123',
        'object' => 'event',
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_123',
                'object' => 'checkout.session',
                'payment_status' => 'paid',
                'payment_intent' => 'pi_test_123',
                'amount_total' => 100000,
                'metadata' => [
                    'studio_session_id' => (string) $studioSession->id,
                    'user_id' => (string) $musician->id,
                ],
            ],
        ],
    ], JSON_THROW_ON_ERROR);

    $request = signedStripeRequest($payload, 'whsec_test_secret');

    $response = app(PaymentController::class)->webhook($request);

    expect($response->getStatusCode())->toBe(200);

    $studioSession->refresh();

    expect($studioSession->payment_status)->toBe('paid');
    expect($studioSession->status)->toBe('confirmed');
    expect($studioSession->stripe_checkout_session_id)->toBe('cs_test_123');
    expect($studioSession->stripe_payment_intent_id)->toBe('pi_test_123');
    expect((float) $studioSession->amount_paid)->toBe(1000.0);
    expect($studioSession->paid_at)->not->toBeNull();
});

test('stripe webhook with invalid signature does not update payment', function () {
    config([
        'services.stripe.webhook_secret' => 'whsec_test_secret',
    ]);

    $producer = User::factory()->producer()->create();
    $musician = User::factory()->musician()->create();

    $studio = Studio::factory()->active()->create([
        'owner_id' => $producer->id,
    ]);

    $studioSession = StudioSession::factory()->confirmed()->create([
        'studio_id' => $studio->id,
        'booked_by' => $musician->id,
        'payment_status' => 'pending',
        'total_price' => 1000,
        'paid_at' => null,
    ]);

    $payload = json_encode([
        'id' => 'evt_test_invalid',
        'object' => 'event',
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_invalid',
                'object' => 'checkout.session',
                'payment_status' => 'paid',
                'payment_intent' => 'pi_test_invalid',
                'amount_total' => 100000,
                'metadata' => [
                    'studio_session_id' => (string) $studioSession->id,
                    'user_id' => (string) $musician->id,
                ],
            ],
        ],
    ], JSON_THROW_ON_ERROR);

    $request = Request::create(
        uri: '/stripe/webhook',
        method: 'POST',
        parameters: [],
        cookies: [],
        files: [],
        server: [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => 'invalid-signature',
        ],
        content: $payload
    );

    $response = app(PaymentController::class)->webhook($request);

    expect($response->getStatusCode())->toBe(400);

    $studioSession->refresh();

    expect($studioSession->payment_status)->toBe('pending');
    expect($studioSession->paid_at)->toBeNull();
});
