<?php

namespace Karadiff\Controllers;

use Karadiff\Helpers\TemplateHelper;

class HomeController extends Controller
{
    public function index()
    {
        $content = TemplateHelper::render('welcome.html', [
            'title' => 'Welcome'
        ]);

        $this->response->setContent($content);
        $this->response->send();
    }
}
