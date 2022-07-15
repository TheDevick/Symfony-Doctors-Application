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

    public function checkBodyKeyExists(string $key, callable $filter = null): bool
    {
        $bodyKeys = $this->getBodyKeys();

        if (!$bodyKeys) {
            return false;
        }

        $array = $bodyKeys;

        if (!is_null($filter)) {
            $array = array_map($filter, $bodyKeys);
        }

        return in_array($key, $array);
    }

    public function toAllParametersBody(\Closure $closjure)
    {
        $body = $this->getBody();

        if (!$body) {
            return false;
        }

        foreach ($body as $key => $value) {
            $closjure($key, $value);
        }
    }
}
