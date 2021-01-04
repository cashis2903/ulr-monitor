<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\UrlMonitorServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Requests\UrlMonitorRequest;
use Exception;


class MonitorController extends Controller
{
    private UrlMonitorServiceInterface $urlMonitorService;

    public function __construct(UrlMonitorServiceInterface $urlMonitorService)
    {
        $this->urlMonitorService = $urlMonitorService;
    }

    public function getSamplesByUrl(string $url): JsonResponse 
    {
        try
        {
            return $this->urlMonitorService->getByUrlWithSamplesTimeLimit($url);
        }
        catch(Exception $e)
        {
            return $this->urlMonitorService->getErrorResponse($e);
        }    
    }

    public function store(UrlMonitorRequest $request): JsonResponse
    {
        try
        {
            $links = $this->urlMonitorService->createLinks($request->get('urls'));
            
            sleep(2);
            
            return (new JsonResponse(null, Response::HTTP_OK))
            ->header('X-Stats', $this->urlMonitorService->getXStats($links));
        }
        catch(Exception $e)
        {
            return $this->urlMonitorService->getErrorResponse($e);
        }    
    }
}
