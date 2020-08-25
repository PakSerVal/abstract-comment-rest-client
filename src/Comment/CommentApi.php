<?php declare(strict_types=1);

namespace pakman\RestClient\Comment;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class CommentApi
{
    private $http;
    private $baseUrl;

    public function __construct(ClientInterface $http, string $baseUrl)
    {
        $this->http = $http;
        $this->baseUrl = $baseUrl;
    }

    public function getList(?int $page = 1) : array
    {
        $response = $this->request('GET', '/comments', ['page' => $page]);

        $comments = json_decode((string)$response->getBody(), true);
        $result = [];
        foreach ($comments as $comment)
        {
            $result[] = $this->convertToModel($comment);
        }

        return $result;
    }

    public function update(int $id, ?string $name, ?string $text) : Comment
    {
        $params = [
            'name' => $name,
            'text' => $text,
        ];
        $response = $this->request('PUT', "/comment/$id", ['json' => $params]);
        $updatedComment = json_decode((string)$response->getBody(), true);

        return $this->convertToModel($updatedComment);
    }

    public function create(string $name, string $text) : Comment
    {
        $params = [
            'name' => $name,
            'text' => $text,
        ];
        $response = $this->request('POST', '/comment', ['json' => $params]);
        $createdComment = json_decode((string)$response->getBody(), true);

        return $this->convertToModel($createdComment);
    }

    private function request(string $method, string $uri, array $params) : ResponseInterface
    {
        $url = $this->generateUrl($uri);

        return $this->http->request($method, $url, $params);
    }

    private function generateUrl(string $uri) : string
    {
        return $this->baseUrl . $uri;
    }

    private function convertToModel(array $comment) : Comment
    {
        $model = new Comment();
        $model->id = $comment['id'];
        $model->name = $comment['name'];
        $model->text = $comment['text'];

        return $model;
    }
}