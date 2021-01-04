<?php

namespace App\Services\Interfaces;

use App\Models\Link;

interface LinkMeasureServiceInterface
{
    public function measure(Link $link): void;
}