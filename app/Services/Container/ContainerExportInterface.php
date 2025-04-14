<?php

namespace App\Services\Container;

use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

interface ContainerExportInterface
{
    public function export(Collection $containers): Response;
} 