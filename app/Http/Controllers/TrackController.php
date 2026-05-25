<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTrackRequest;
use App\Models\StudioSession;
use App\Models\Track;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TrackController extends Controller
{
    use AuthorizesRequests;

    public function store(StoreTrackRequest $request, StudioSession $studioSession): RedirectResponse
    {
        foreach ($request->file('tracks', []) as $file) {
            $path = $file->store('tracks/session-'.$studioSession->id);

            $studioSession->tracks()->create([
                'uploaded_by' => $request->user()->id,
                'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
                'size' => $file->getSize(),
                'version' => '1.0',
            ]);
        }

        return redirect()
            ->route('studio-sessions.show', $studioSession)
            ->with('status', 'Archivos subidos correctamente.');
    }

    public function download(Track $track): StreamedResponse
    {
        $this->authorize('view', $track);

        return Storage::download($track->path, $track->original_name);
    }

    public function destroy(Track $track): RedirectResponse
    {
        $this->authorize('delete', $track);

        $studioSession = $track->studioSession;

        Storage::delete($track->path);

        Track::destroy($track->getKey());

        return redirect()
            ->route('studio-sessions.show', $studioSession)
            ->with('status', 'Archivo eliminado correctamente.');
    }
}
