<?php
declare(strict_types=1);

/*
 * This file is part of the `dreadwarrior/talk-resilience-dfm-tc` project.
 *
 * (c) 2019 Thomas Juhnke <contact@dreadlabs.de>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace DreadLabs\ResilienceTalk;

use GuzzleHttp\ClientInterface as HttpClientInterface;
use Predis\ClientInterface as RedisClientInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class Application
{
    /** @var RedisClientInterface */
    private $redisClient;

    /** @var HttpClientInterface */
    private $httpClient;

    /**
     */
    public function __construct(RedisClientInterface $redisClient, HttpClientInterface $httpClient)
    {
        $this->redisClient = $redisClient;
        $this->httpClient = $httpClient;
    }

    public function run(StreamedResponse $response): void
    {
        $response->setCallback(function () {
            $this->runRedis();
            flush();

            $this->runSeparator();
            flush();

            $this->runHttp();
            flush();
        });
    }

    private function runRedis(): void
    {
        printf('Sequence before: %u%s', (int)$this->redisClient->get('sequence'), chr(10));

        $this->redisClient->incr('sequence');

        printf('Sequence after: %u%s', (int)$this->redisClient->get('sequence'), chr(10));
    }

    private function runSeparator(): void
    {
        printf('%s%s%s%s', chr(10), str_repeat('-', 80), chr(10), chr(10));
    }

    private function runHttp(): void
    {
        $response = $this->httpClient->request(
            'GET',
            sprintf('/test?time=%s', time()),
            ['accept' => 'application/json']
        );

        printf('HTTP echo: %s%s', print_r(json_decode($response->getBody()->getContents(), true), true), chr(10));
    }
}
