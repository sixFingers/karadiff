<?php

namespace Karadiff\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    protected $request;
    protected $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;

        // Let's assume we won't make any json for now.
        $this->response->headers->set('Content-Type', 'text/html');
    }
}
