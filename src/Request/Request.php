<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class Request extends HttpFoundationRequest
{
    public readonly RequestExtractor $extractor;

    /**
     * Create Custom Request Object.
     */
    public static function createRequest(): Request
    {
        $request = Request::createFromGlobals();

        $request->extractor = new RequestExtractor($request);

        return $request;
    }

    /**
     * Return the Request Body.
     * Return False if Body Content is Null.
     */
    public function getBody(): array|bool
    {
        $bodyContent = $this->getContent();

        if (!$bodyContent) {
            return false;
        }

        $body = $this->toArray();

        return $body;
    }

    /**
     * Return an Array with the Request Body Parameters
     * Return False if Body Content is Null.
     */
    public function getBodyParameters(): array|bool
    {
        $body = $this->getBody();

        if (!$body) {
            return false;
        }

        $bodyParameters = array_values($body);

        return $bodyParameters;
    }

    /**
     * Return an Array with the Request Body Keys
     * Return False if Body Content is Null.
     */
    public function getBodyKeys(): array|bool
    {
        $body = $this->getBody();

        if (!$body) {
            return false;
        }

        $bodyKeys = array_keys($body);

        return $bodyKeys;
    }

    /**
     * Return the Parameter from Request Body
     * Return False if Body Content is Null.
     */
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

    /**
     * Check if Parameter Exists in Body Request
     * Return False if Body Content is Null.
     */
    public function checkBodyParameterExists(string $parameter, callable $filter = null): bool
    {
        $bodyParameters = $this->getBodyKeys();

        if (!$bodyParameters) {
            return false;
        }

        $array = $bodyParameters;

        if (!is_null($filter)) {
            $array = array_map($filter, $bodyParameters);
        }

        return in_array($parameter, $array);
    }

    /**
     * Check if Key Exists in Body Request
     * Return False if Body Content is Null.
     *
     * @param callable $filter
     */
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

    /**
     * Run a Closjure to All Parameters in Body
     * Return False if Body Content is Null.
     *
     * @param string   $key
     * @param callable $filter
     */
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
