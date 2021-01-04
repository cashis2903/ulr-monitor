<?php

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Link;
use Illuminate\Http\JsonResponse;

interface UrlMonitorServiceInterface
{
    public function getByUrlWithSamplesTimeLimit(string $url): ?JsonResponse;
}