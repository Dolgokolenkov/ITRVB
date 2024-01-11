<?php

namespace tests\Model;

use PHPUnit\Framework\TestCase;
use src\Model\Name;
use src\Model\User;
use src\Model\UUID;

class UserTests extends TestCase
{
    public function testGetData(): void {
        $uuid = UUID::random();
        $username = 'username';
        $name = new Name('fN', 'lN');
        $user = new User($uuid, $username, $name);

        $this->assertEquals($uuid, $user->getUuid());
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($name, $user->getName());
    }
}