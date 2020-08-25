<?php declare(strict_types=1);

namespace pakman\RestClient\tests;

use donatj\MockWebServer\MockWebServer;
use pakman\RestClient\ClientBuilder;
use pakman\RestClient\Comment\CommentApi;
use PHPUnit\Framework\TestCase;

abstract class ApiTestCase extends TestCase
{
    protected $server;

    protected function setUp(): void
    {
        $this->server = new MockWebServer(8085, '127.0.0.1');
        $this->server->start();
    }

    protected function tearDown(): void
    {
        $this->server->stop();
    }

    protected function getCommentApiClient() : CommentApi
    {
        return ClientBuilder::createCommentApiClient($this->server->getServerRoot());
    }
}