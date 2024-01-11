<?php
namespace myHttp;

class ErrorResponse extends Response
{
    protected const SUCCESS = false;

    public function __construct(
        private string $reason = 'Wrong query'
    ) {}

    protected function payload(): array
    {
        return ['reason' => $this->reason];
    }

    public function getBody(): string
    {
        return json_encode($this->reason);
    }
}