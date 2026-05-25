<?php

namespace App\Http\Controllers;

use App\Models\StudioSession;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\Response;

class SplitSheetController extends Controller
{
    use AuthorizesRequests;

    public function download(StudioSession $studioSession): Response
    {
        $this->authorize('view', $studioSession);

        $studioSession->load([
            'studio.owner',
            'booker',
            'musicians',
        ]);

        $fileName = 'split-sheet-session-'.$studioSession->id.'.pdf';

        return Pdf::loadView('pdf.split-sheet', [
            'studioSession' => $studioSession,
        ])
            ->setPaper('letter')
            ->download($fileName);
    }
}
