<?php

namespace App\Repositories;

use App\Models\Link;
use App\Models\LinkSample;
use App\Repositories\Interfaces\LinkSampleRepositoryInterface;

class LinkSampleRepository implements LinkSampleRepositoryInterface
{
    public function createOne(Link $link, array $data): LinkSample
    {
        return $link->samples()->create($data)->refresh();
    } 

    // {
    //     return Link::get();
    // }

    // public function getAllWithSamples(): Collection
    // {
    //     return Link::with('samples')->get();
    // }
}
