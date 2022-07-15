<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class Request extends HttpFoundationRequest
{
    public static function createRequest(): Request
    {
        return Request::createFromGlobals();
    }

    public function getBody(): array|false
    {
        if (is_null($this->getContent())) {
            return false;
        }

        $body = $this->toArray();

        return $body;
    }

    public function getBodyValues(): array|false
    {
        $body = $this->getBody();

        if (!$body) {
            return false;
        }

        $bodyValues = array_values($body);

        return $bodyValues;
    }

    public function getBodyKeys(): array|false
    {
        $body = $this->getBody();

        if (!$body) {
            return false;
        }

        $bodyKeys = array_keys($body);

        return $bodyKeys;
    }

    public function getParameterBody(string $parameter, $default = null): mixed
    {
        $body = $this->getBody();

        if (!$body) {
            return false;
        }

        if (!$this->checkBodyValueExists($parameter)) {
            return $default;
        }

        $value = $body[$parameter];

        return $value;
    }

    public function checkBodyValueExists(string $value, callable $filter = null): bool
    {
        $bodyValues = $this->getBodyValues();

        if (!$bodyValues) {
            return false;
        }

        $array = $bodyValues;

        if (!is_null($filter)) {
            $array = array_map($filter, $bodyValues);
        }

        return in_array($value, $array);
    }
}
