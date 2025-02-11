<?php

namespace App\Spiders;

use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;

class MetadataSpider extends BasicSpider
{
    public array $startUrls = [];

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
    ];

    public array $extensions = [
        LoggerExtension::class,
        StatsCollectorExtension::class,
    ];

    public int $concurrency = 2;

    public int $requestDelay = 1;


    /**
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {
        yield $this->item([
            'title' => $response->filter('title')->text(),
            'description' => $response->filter('meta[name="description"]')->attr('content'),
        ]);
    }
}
