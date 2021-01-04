<?php

namespace App\Repositories;

use App\Models\Link;
use App\Repositories\Interfaces\LinkRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class LinkRepository implements LinkRepositoryInterface
{
    public function create(string $url): Link
    {
        return Link::updateOrCreate(['url' => $url]);
    } 

    public function getAll(): Collection
    {
        return Link::get();
    }

    public function getAllWithSamples(): Collection
    {
        return Link::with('samples')->get();
    }

    public function getByUrlWithSamplesTimeLimit(string $url, int $minuteLimit): ?Link
    {
        return Link::with(['samples' => function ($query) use ($minuteLimit){
                return $query->where('created_at', '>=', Carbon::now()->subMinutes($minuteLimit));
            }])
            ->where('url', '=', $url)
            ->first();
    }
}
