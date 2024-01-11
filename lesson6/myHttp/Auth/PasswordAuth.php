<?php

namespace myHttp\Auth;

use myHttp\ErrorResponse;
use myHttp\Request;
use src\Exceptions\AuthException;
use src\Exceptions\HttpException;
use src\Exceptions\UserNotFoundException;
use src\Model\User;
use src\Repositories\UserRepositoryInterface;

class PasswordAuth implements PasswordAuthInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) { }

    public function user(Request $request): User
    {
        $data = $request->body(['username', 'password']);

        try {
            $user = $this->userRepository->getByUsername($data['username']);
        } catch (UserNotFoundException $ex) {
            return new ErrorResponse($ex->getMessage());
        }

        if (!$user->checkPassword($data['password'])) {
            throw new AuthException('Wrong password');
        }

        return $user;
    }
}