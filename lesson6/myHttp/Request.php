<?php

namespace myHttp;

use src\Exceptions\HttpException;

class Request
{
    public function __construct(
        private array $get,
        private array $post,
        private array $server
    ) {}

    public function path(): string
    {
        if (!array_key_exists('REQUEST_URI', $this->server)) {
            throw new HttpException('Cannot found query');
        }

        $components = parse_url($this->server['REQUEST_URI']);

        if (!is_array($components) || !array_key_exists('path', $components)) {
            throw new HttpException('Cannot found path query');
        }

        return $components['path'];
    }

    public function query(string $param): string
    {
        if (!array_key_exists($param, $this->get)) {
            throw new HttpException('Incorrect param for query');
        }

        $value = trim($this->get[$param]);

        if (empty($value)) {
            throw new HttpException('Not found param');
        }

        return $value;
    }

    public function body(array $params): array
    {
        $values = [];
        foreach ($params as $param) {
            if (!array_key_exists($param, $this->post)) {
                throw new HttpException("Incorrect param for body: $param");
            }

            $value = trim($this->post[$param]);

            if (empty($value)) {
                throw new HttpException("Not found body param: $param");
            }

            $values[$param] = $value;
        }

        return $values;
    }

    public function header(string $header): string
    {
        $headerName = mb_strtoupper("http_" . str_replace("-", '_', $header));

        if (!array_key_exists($headerName, $this->server)) {
            throw new HttpException('Header not found');
        }

        $value = trim($this->server[$headerName]);

        if (empty($value)) {
            throw new HttpException('Header empty');
        }

        return $value;
    }

    public function method(): string
    {
        if (!array_key_exists('REQUEST_METHOD', $this->server)) {
            throw new HttpException('Cannot get method from request');
        }

        return $this->server['REQUEST_METHOD'];
    }
}