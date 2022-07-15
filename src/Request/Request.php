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

    public function getBodyParameters(): array|false
    {
        $body = $this->getBody();

        if (!$body) {
            return false;
        }

        $bodyParameters = array_values($body);

        return $bodyParameters;
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

    public function getParameterBody(string $parameter, mixed $default = null): mixed
    {
        $body = $this->getBody();

        if (!$body) {
            return false;
        }

        if (!$this->checkBodyParameterExists($parameter)) {
            return $default;
        }

        $parameter = $body[$parameter];

        return $parameter;
    }

    public function checkBodyParameterExists(string $parameter, callable $filter = null): bool
    {
        $bodyParameters = $this->getBodyParameters();

        if (!$bodyParameters) {
            return false;
        }

        $array = $bodyParameters;

        if (!is_null($filter)) {
            $array = array_map($filter, $bodyParameters);
        }

        return in_array($parameter, $array);
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

    public function toAllParametersBody(\Closure $closjure): bool
    {
        $body = $this->getBody();

        if (!$body) {
            return false;
        }

        foreach ($body as $key => $value) {
            $closjure($key, $value);
        }

        return true;
    }
}
