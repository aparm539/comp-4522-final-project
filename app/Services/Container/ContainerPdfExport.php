<?php

namespace App\Services\Container;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

class ContainerPdfExport implements ContainerExportInterface
{
    public function export(Collection $containers): Response
    {
        $pdf = PDF::loadView('containers.print', ['containers' => $containers]);
        
        return response()->streamDownload(
            fn() => echo $pdf->stream(),
            'containers.pdf'
        );
    }
} 