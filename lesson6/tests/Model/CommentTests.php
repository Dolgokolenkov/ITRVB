<?php

namespace tests\Model;

use PHPUnit\Framework\TestCase;
use src\Model\Comment;
use src\Model\UUID;

class CommentTests extends TestCase
{
    public function testGetData(): void {
        $uuid = UUID::random();
        $authorUuid = UUID::random();
        $articleUuid = UUID::random();
        $text = 'Text';
        $comment = new Comment(
            $uuid,
            $authorUuid,
            $articleUuid,
            $text
        );

        $this->assertEquals($uuid, $comment->getUuid());
        $this->assertEquals($authorUuid, $comment->getAuthorUuid());
        $this->assertEquals($articleUuid, $comment->getPostUuid());
        $this->assertEquals($text, $comment->getText());
    }
}