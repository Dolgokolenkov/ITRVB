<?php

namespace myHttp\Actions\Auth;

use DateTimeImmutable;
use myHttp\Actions\ActionInterface;
use myHttp\Auth\PasswordAuthInterface;
use myHttp\ErrorResponse;
use myHttp\Request;
use myHttp\Response;
use myHttp\SuccessfullResponse;
use src\Exceptions\AuthException;
use src\Model\AuthToken;
use src\Repositories\AuthTokenRepositoryInterface;

class Login implements ActionInterface
{
    public function __construct(
        private PasswordAuthInterface $passwordAuth,
        private AuthTokenRepositoryInterface $authTokenRepository
    ) { }
    
    public function handle(Request $request): Response
    {
        try {
            $user = $this->passwordAuth->user($request);
        } catch (AuthException $error) {
            return new ErrorResponse($error->getMessage());
        }

        $authToken = new AuthToken(
            bin2hex(random_bytes(40)),
            $user->getUuid(),
            (new DateTimeImmutable())->modify('+1 day')
        );

        $this->authTokenRepository->save($authToken);

        return new SuccessfullResponse([
            'token' => (string)$authToken,
        ]);
    }
}