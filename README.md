# Client for abstract comment service 

Клиент для абстрактного сервиса комментариев 

## Установка

```bash
composer require abstract-comment-api/php-rest-client
```

#### Пример использования
```php
use pakman\RestClient\ClientBuilder;

$commentApi = ClientBuilder::createCommentApiClient('https://example.com');

$comment = $commentApi->create('User name', 'User comment');
$commentApi->update($comment->id, 'User updated name', 'User updated comment');

$comments = $commentApi->getList();
foreach ($comments as $comment)
{
    echo $comment->id;
    echo $comment->name;
    echo $comment->text;
}
```
