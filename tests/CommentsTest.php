<?php

namespace pakman\RestClient\tests;

use donatj\MockWebServer\RequestInfo;
use donatj\MockWebServer\Response;
use donatj\MockWebServer\ResponseStack;
use GuzzleHttp\Exception\ClientException;
use pakman\RestClient\Comment\Comment;

class CommentsTest extends ApiTestCase
{
    public function testGetComments() : void
    {
        $comments = [
            [
                'id' => '1',
                'name' => 'Foo',
                'text' => 'The best ever Foo.',
            ],
            [
                'id' => '2',
                'name' => 'Bar',
                'text' => 'The best ever Bar.',
            ],
        ];

        $this->server->setResponseOfPath(
            '/comments',
            new ResponseStack(
                new Response(json_encode($comments), [], 200)
            )
        );

        $result = $this->getCommentApiClient()->getList();

        $expectedResult = [];
        foreach ($comments as $comment)
        {
            $model = new Comment();
            $model->id = $comment['id'];
            $model->name = $comment['name'];
            $model->text = $comment['text'];

            $expectedResult[] = $model;
        }
        $this->assertEquals($expectedResult, $result);
        $this->assertEquals('GET', $this->server->getLastRequest()->jsonSerialize()[RequestInfo::JSON_KEY_METHOD]);
    }

    public function testCreateComment() : void
    {
        $newComment = [
            'id' => 3,
            'name' => 'newName',
            'text' => 'newText',
        ];
        $this->server->setResponseOfPath(
            '/comment',
            new ResponseStack(
                new Response(json_encode($newComment), [], 201),
                new Response('', [], 400)
            )
        );

        $expectedResult = new Comment();
        $expectedResult->id = $newComment['id'];
        $expectedResult->name = $newComment['name'];
        $expectedResult->text = $newComment['text'];

        $result = $this->getCommentApiClient()->create('newName', 'newText');
        $this->assertEquals($expectedResult, $result);

        $this->assertEquals('POST', $this->server->getLastRequest()->jsonSerialize()[RequestInfo::JSON_KEY_METHOD]);
        $this->assertEquals(json_encode(['name' => $newComment['name'], 'text' => $newComment['text']]), $this->server->getLastRequest()->jsonSerialize()[RequestInfo::JSON_KEY_INPUT]);

        $this->expectException(ClientException::class);
        $this->expectExceptionCode(400);
        $this->getCommentApiClient()->create('', '');
    }

    public function testUpdateComment() : void
    {
        $updatedComment = [
            'id' => 2,
            'name' => 'updatedName',
            'text' => 'updatedText',
        ];
        $this->server->setResponseOfPath(
            '/comment/2',
            new ResponseStack(new Response(json_encode($updatedComment), [], 200)),
            new ResponseStack(new Response('', [], 404))
        );

        $expectedResult = new Comment();
        $expectedResult->id = $updatedComment['id'];
        $expectedResult->name = $updatedComment['name'];
        $expectedResult->text = $updatedComment['text'];

        $result = $this->getCommentApiClient()->update(2, 'updatedName', 'updatedText');
        $this->assertEquals($expectedResult, $result);

        $this->assertEquals('PUT', $this->server->getLastRequest()->jsonSerialize()[RequestInfo::JSON_KEY_METHOD]);
        $this->assertEquals(json_encode(['name' => $updatedComment['name'], 'text' => $updatedComment['text']]), $this->server->getLastRequest()->jsonSerialize()[RequestInfo::JSON_KEY_INPUT]);

        $this->expectException(ClientException::class);
        $this->expectExceptionCode(404);
        $this->getCommentApiClient()->update(2, 'updatedName', 'updatedText');
    }
}
