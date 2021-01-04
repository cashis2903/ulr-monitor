<?php

namespace App\Repositories\Interfaces;

use App\Models\Link;
use Illuminate\Database\Eloquent\Collection;

interface LinkRepositoryInterface
{
    public function create(string $url): Link; 
    public function getAllWithSamples(): Collection;
    public function getAll(): Collection;
    public function getByUrlWithSamplesTimeLimit(string $url, int $minuteLimit): ?Link;
}
