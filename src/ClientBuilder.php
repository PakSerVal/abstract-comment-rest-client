<?php declare(strict_types=1);

namespace pakman\RestClient;

use GuzzleHttp\Client as HttpClient;
use pakman\RestClient\Comment\CommentApi;

class ClientBuilder
{
    public static function createCommentApiClient(string $baseUrl) : CommentApi
    {
        $http = new HttpClient();

        return new CommentApi($http, $baseUrl);
    }
}