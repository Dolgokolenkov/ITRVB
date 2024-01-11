<?php

namespace myHttp\Actions\Auth;

use myHttp\Actions\ActionInterface;
use myHttp\ErrorResponse;
use myHttp\Request;
use myHttp\Response;
use myHttp\SuccessfullResponse;
use src\Exceptions\AuthException;
use src\Exceptions\HttpException;
use src\Repositories\AuthTokenRepositoryInterface;

class Logout implements ActionInterface
{
    private const HEADER_PREFIX = 'Bearer ';
    public function __construct(
        private AuthTokenRepositoryInterface $authTokenRepository
    ) { }
    public function handle(Request $request): Response
    {
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $error) {
            throw new AuthException($error->getMessage());
        }

        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }

        $token = mb_substr($header, strlen(self::HEADER_PREFIX));

        try {
            $this->authTokenRepository->reset($token);

            return new SuccessfullResponse([
                'token' => $token,
            ]);
        } catch (\Exception $error) {
            return new ErrorResponse($error->getMessage());
        }
    }
}