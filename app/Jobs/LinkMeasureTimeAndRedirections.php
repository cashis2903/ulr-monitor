<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Interfaces\LinkMeasureServiceInterface;
use App\Repositories\Interfaces\LinkRepositoryInterface;
use Illuminate\Support\Facades\Log;
use App\Models\Link;

class LinkMeasureTimeAndRedirections implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Link $link;

    public function __construct(Link $link)
    {
        $this->link = $link;
    }

    public function handle(LinkMeasureServiceInterface $linkMeasureServiceInterface)
    {
        $linkMeasureServiceInterface->measure($this->link);
    }
}
