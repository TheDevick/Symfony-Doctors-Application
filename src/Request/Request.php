<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class Request extends HttpFoundationRequest
{
    public static function createRequest(): Request
    {
        return Request::createFromGlobals();
    }
}
