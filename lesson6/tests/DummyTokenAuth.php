<?php

namespace tests;

use myHttp\Auth\TokenAuthInterface;
use myHttp\Request;
use src\Model\Name;
use src\Model\User;
use src\Model\UUID;

class DummyTokenAuth implements TokenAuthInterface
{

    public function user(Request $request): User
    {
        return new User(
            new UUID('c89457cb-27b6-4ed4-bc90-503f1b47a2dc'),
            'username',
            'password',
            new Name(
                'fN',
                'lN'
            )
        );
    }
}