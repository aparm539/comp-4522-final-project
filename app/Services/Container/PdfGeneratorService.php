<?php

declare(strict_types=1);

namespace App\Services\Container;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class PdfGeneratorService implements PdfGeneratorServiceInterface
{
    public function streamPdfDownload(Collection $containers): StreamedResponse
    {

        $response = response()->streamDownload(function () use ($containers) {
            $pdf = $this
                ->generatePDF($containers)
                ->stream();
            // TODO Figure out why removing echo causes PDF to not be downloaded.
            echo $pdf;
        }, 'containers.pdf');

        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }

    public function generatePDF(Collection $containers): \Barryvdh\DomPDF\PDF
    {
        return PDF::loadView('containers.print', ['containers' => $containers]);
    }
}
