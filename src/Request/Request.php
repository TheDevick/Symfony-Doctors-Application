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
}
