<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudioSessionRequest;
use App\Jobs\SendSessionReservedEmail;
use App\Models\Studio;
use App\Models\StudioSession;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudioSessionController extends Controller
{
    use AuthorizesRequests;

    public function create(Studio $studio): View
    {
        $this->authorize('create', [StudioSession::class, $studio]);

        $musicians = User::query()
            ->where('role', 'musician')
            ->whereKeyNot(Auth::id())
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('studio-sessions.create', [
            'studio' => $studio,
            'musicians' => $musicians,
        ]);
    }

    public function store(StoreStudioSessionRequest $request, Studio $studio): RedirectResponse
    {
        $validated = $request->validated();

        $studioSession = $studio->studioSessions()->create([
            ...$request->sessionData((float) $studio->hourly_rate),
            'booked_by' => $request->user()->id,
        ]);

        $studioSession->musicians()->attach($request->user()->id, [
            'instrument' => $validated['instrument'],
            'payment_split' => $validated['payment_split'],
        ]);

        foreach (($validated['participants'] ?? []) as $participant) {
            if (blank($participant['user_id'] ?? null)) {
                continue;
            }

            $studioSession->musicians()->attach((int) $participant['user_id'], [
                'instrument' => $participant['instrument'],
                'payment_split' => $participant['payment_split'],
            ]);
        }

        SendSessionReservedEmail::dispatch($studioSession->id);

        return redirect()
            ->route('studio-sessions.show', $studioSession)
            ->with('status', 'Sesión reservada correctamente.');
    }

    public function show(StudioSession $studioSession): View
    {
        $this->authorize('view', $studioSession);

        $studioSession->load([
            'studio.owner',
            'booker',
            'musicians',
            'tracks.uploader',
        ]);

        return view('studio-sessions.show', [
            'studioSession' => $studioSession,
        ]);
    }

    public function destroy(StudioSession $studioSession): RedirectResponse
    {
        $this->authorize('cancel', $studioSession);

        $studioSession->status = 'cancelled';
        $studioSession->save();

        return redirect()
            ->route('studios.show', $studioSession->studio)
            ->with('status', 'Sesión cancelada correctamente.');
    }
}
