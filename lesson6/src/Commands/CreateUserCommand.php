<?php

namespace src\Commands;

use Psr\Log\LoggerInterface;
use src\Exceptions\CommandException;
use src\Exceptions\UserNotFoundException;
use src\Model\Name;
use src\Model\User;
use src\Repositories\UserRepositoryInterface;

class CreateUserCommand {
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger
    ) {
    }

    public function userExist(string $username): bool {
        try {
            $this->userRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }

        return true;
    }

    public function handle(Arguments $arguments): void {
        $this->logger->info('Create user command start');

        $username = $arguments->get('username');
        $password = $arguments->get('password');

        if ($this->userExist($username)) {
            $this->logger->warning("User already exists: $username");
            throw new CommandException(
                "User already exists: $username"
            );
        }

        $user = User::createForm(
            $username,
            $password,
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name')
            )
        );

        $this->userRepository->save($user);

        $this->logger->info("User created with UUID {$user->getUuid()}");
    }
}