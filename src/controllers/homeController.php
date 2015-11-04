<?php

namespace Karadiff\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        $this->response->setContent('<h1>Index</h1>');
        $this->response->send();
    }
}
