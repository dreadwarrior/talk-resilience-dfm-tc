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

use DreadLabs\ResilienceTalk\Application;
use GuzzleHttp\Client as HttpClient;
use Predis\Client as RedisClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

require_once __DIR__ . '/../../../vendor/autoload.php';

$app = new Application(
    new RedisClient('tcp://redis:6379'),
    new HttpClient(['base_uri' => 'http://http-echo'])
);

$response = new StreamedResponse(null, Response::HTTP_OK, ['content-type' => 'text/plain']);

$app->run($response);

$response->send();
