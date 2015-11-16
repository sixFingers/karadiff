<?php

namespace Karadiff\Controllers;

use Karadiff\Helpers\TemplateHelper;
use Karadiff\Diff\Differ;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function index()
    {
        $additionsCount = $removalsCount = 0;
        $original = $this->request->request->get('original', '');
        $changed = $this->request->request->get('changed', '');
        $differ = null;

        if ($this->request->isMethod(Request::METHOD_POST)) {
            $differ = new Differ($original, $changed);
        }

        $content = TemplateHelper::render('welcome.html', array(
            'title' => 'Diff',
            'differ' => $differ,
            'original' => $original,
            'changed' => $changed
        ));

        $this->response->setContent($content);
        $this->response->send();
    }
}
