<?php

declare(strict_types=1);

namespace App\Services\Container;

use Barryvdh\DomPDF\PDF;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface PdfGeneratorServiceInterface
{
    public function streamPdfDownload(Collection $containers): Response|JsonResponse|StreamedResponse;

    public function generatePDF(Collection $containers): PDF;
}
