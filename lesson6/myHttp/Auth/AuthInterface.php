<?php

namespace myHttp\Auth;

use myHttp\Request;
use src\Model\User;

interface AuthInterface
{
    public function user(Request $request): User;
}