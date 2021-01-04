<?php

namespace App\Services;

use App\Services\Interfaces\LinkMeasureServiceInterface;
use App\Repositories\Interfaces\LinkSampleRepositoryInterface;
use App\Models\Link;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\TransferStats;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use DOMDocument;
use DOMXPath;


class LinkMeasureService implements LinkMeasureServiceInterface
{
    private CONST REGEX = '#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#';
    private CONST NUMBER_META_REDIRECTS = 100;

    private LinkSampleRepositoryInterface $linkSampleRepository;

    public function __construct(
        LinkSampleRepositoryInterface $linkSampleRepository
    )
    {
        $this->linkSampleRepository = $linkSampleRepository;
    }

    public function measure(Link $link): void
    {
       if($data = $this->exploreLink($link->url))
       {
           $this->linkSampleRepository->createOne($link, $this->prepare($data));
           Log::info('job: ' . $link->url);
       }
       
    }

    private function exploreLink(string $url, int $redirectionsCounter = 0, float $timeTotal = 0): ?array
    {
        $response = $this->getHttpResponse($url, $timeTotal);

        if(!$response)
        {
            return null;
        }

        $redirectionsCounter += count($response->getHeader('X-Guzzle-Redirect-Status-History'));

        if($metaRefreshUrl = $this->checkMetaTagsRedirection($response->getBody()))
        {
            return $this->exploreLink(trim($metaRefreshUrl), $redirectionsCounter + 1, $timeTotal);
        }

        return ['redirects' => $redirectionsCounter, 'totalTime' => $timeTotal];
    }

    private function getHttpResponse(string $url, float &$timeTotal)
    {
        $client = new Client([
            'allow_redirects' => [
                'max'             => 10,        
                'referer'         => true,
                'track_redirects' => true,
                'timeout' => 30.0,
            ],
        ]);
    
        try
        {
            return $client->request('GET', $url, [
                'on_stats' => function (TransferStats $stats) use (&$timeTotal, &$effectiveUri)
                {
                    $timeTotal += $stats->getTransferTime();
                    $effectiveUri = $stats->getEffectiveUri();
                }
            ]); 
        } 
        catch (RequestException $e) 
        {
            Log::info($e->getRequest());
            return false;
        }
    }

    private function checkMetaTagsRedirection(?string $body): ?string
    {
        if (!$body)
        {
            return null;
        } 

        $doc = new DOMDocument();
        @$doc->loadHTML($body);

        $xpath = new DOMXPath($doc);
        $description = $xpath->query('//meta[@http-equiv="refresh"]/@content');

        if(empty($description->item(0)->nodeValue))
        {
            return null;
        }
        
        preg_match(self::REGEX, $description->item(0)->nodeValue, $matches);
        
        $urlRefresh = $matches[0];

        if(empty($urlRefresh))
        {
            return null;
        }

        return trim($urlRefresh);
    }

    private function prepare(array $data): array
    {
        return [
            'redirections' => $data['redirects'] ?? 0,
            'time' => $data['totalTime'] ?? 0
        ];
    }
}