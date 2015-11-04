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

    protected function template($path, $vars, $return = true)
    {
        $path = __DIR__ . '/../templates/' . $path;
        extract($vars);

        ob_start();
        require_once($path);
        $content = ob_get_clean();

        if(true === $return) {
            return $content;
        } else {
            echo $content;
        }
    }
}
