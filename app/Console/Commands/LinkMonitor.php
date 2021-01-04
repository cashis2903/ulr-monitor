<?php

namespace App\Console\Commands;

use App\Jobs\LinkMeasureTimeAndRedirections;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Repositories\Interfaces\LinkRepositoryInterface;
use Exception;

class LinkMonitor extends Command
{
    protected $signature = 'LinkMonitor:all';

    protected $description = 'Link Monitor Measure Time and Redirections';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(LinkRepositoryInterface $linkRepository)
    {
        $links = $linkRepository->getAll();

        $links->each(function ($link) {
            LinkMeasureTimeAndRedirections::dispatch($link);
        });

    }

    public function failed(Exception $exception)
    {
        Log::info($exception->getMessage());
    }
}
