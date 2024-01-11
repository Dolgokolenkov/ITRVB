<?php

namespace tests\Model;

use PHPUnit\Framework\TestCase;
use src\Model\Post;
use src\Model\UUID;

class ArticleTests extends TestCase
{
    public function testGetData(): void {
        $uuid = UUID::random();
        $authorUuid = UUID::random();
        $title = 'Title1';
        $text = 'Text';
        $article = new Post(
            $uuid,
            $authorUuid,
            $title,
            $text
        );

        $this->assertEquals($uuid, $article->getUuid());
        $this->assertEquals($authorUuid, $article->getAuthorUuid());
        $this->assertEquals($title, $article->getTitle());
        $this->assertEquals($text, $article->getText());
    }
}