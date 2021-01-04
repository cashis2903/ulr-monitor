<?php

namespace App\Services;

use App\Services\Interfaces\UrlMonitorServiceInterface;
use App\Repositories\Interfaces\LinkRepositoryInterface;
use App\Http\Resources\UrlResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Jobs\LinkMeasureTimeAndRedirections;
use Exception;

class UrlMonitorService implements UrlMonitorServiceInterface
{
    private const HISTORY_MINUTE = 10;
    private const MESSAGE_404 = 'URL not found in database';
    private const X_STATS = '"%s": [redirections: %d, time: %f],';
    private const X_STATS_FAIL = '"%s": null,';

    private LinkRepositoryInterface $linkRepository;

    public function __construct(
        LinkRepositoryInterface $linkRepository
    )
    {
        $this->linkRepository = $linkRepository;
    }

    public function createLink(string $url): void
    {
        $this->linkRepository->create($url);
    }

    public function createLinks(array $urls): array
    {
        $links = [];

        foreach($urls as $url)
        {
            $link = $this->linkRepository->create($url);
            
            LinkMeasureTimeAndRedirections::dispatch($link)->onQueue('new'); 
            
            $links[] = $link;
        }

        return $links;
    }

    public function getXStats(array $links)
    {
        foreach($links as $link)
        {
            $data[$link->url] = $this->linkRepository->getByUrlWithSamplesTimeLimit($link->url, 2)->samples;
        }

       return $this->prepareXStats($data);
    }

    public function getByUrlWithSamplesTimeLimit(string $url): ?JsonResponse
    {
        $data = $this->linkRepository->getByUrlWithSamplesTimeLimit($url, self::HISTORY_MINUTE);

        if(!$data)
        {
            return (new JsonResponse($this->getDataNotFound(), Response::HTTP_NOT_FOUND));
        }

        return UrlResource::make($data)->additional(['messages' => 'hello there!'])->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function getErrorResponse(Exception $e)
    {
        $data = [
            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'data' => null,
            'errors' => $e->getMessage()
        ];
      
        return (new JsonResponse($data, Response::HTTP_INTERNAL_SERVER_ERROR));
    } 

    private function getDataNotFound()
    {
        return $data = [
            'code' => Response::HTTP_NOT_FOUND,
            'data' => null,
            'errors' => self::MESSAGE_404
        ];
    }

    private function prepareXStats(array $data)
    {
        $prepared = null;

        foreach ($data as $url => $stats)
        {
            if($stats->count() > 0)
            {
                $prepared .= sprintf(self::X_STATS, $url, $stats->first()->redirections, $stats->first()->time);
            }
            else
            {
                $prepared .= sprintf(self::X_STATS_FAIL, $url);
            }
        }
        
        return rtrim($prepared, ',');
    }
}