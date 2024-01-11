<?php

namespace src\Model;

class Comment {
    public function __construct(
        private UUID $uuid,
        private UUID $authorUuid,
        private UUID $postUuid,
        private string $text) {
    }

    public function getUuid(): UUID {
        return $this->uuid;
    }

    public function getAuthorUuid(): UUID {
        return $this->authorUuid;
    }

    public function getPostUuid(): UUID {
        return $this->postUuid;
    }

    public function getText(): string {
        return $this->text;
    }
}

?>