<?php

namespace Karadiff\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        $content = $this->template('welcome.html', [
            'title' => 'Welcome'
        ]);
        $this->response->setContent($content);
        $this->response->send();
    }
}
