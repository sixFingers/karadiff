<?php

namespace Karadiff\Controllers;

use Karadiff\Helpers\TemplateHelper;
use Karadiff\Diff\Providers\DiffProviderStringWord;
use Karadiff\Diff\Renderers\DiffRendererTextWord;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function index()
    {
        $original = $this->request->request->get('original', '');
        $changed = $this->request->request->get('changed', '');
        $additions = 0;
        $removals = 0;

        if($this->request->isMethod(Request::METHOD_POST)) {
            $provider = new DiffProviderStringWord($original, $changed);
            $renderer = new DiffRendererTextWord($provider);
            $diff = $renderer->render();
            $additions = $provider->additions;
            $removals = $provider->removals;
        }

        $content = TemplateHelper::render('welcome.html', array(
            'title' => 'Diff',
            'original' => $original,
            'changed' => $changed,
            'diff' => $diff,
            'additions' => $additions,
            'removals' => $removals,
        ));

        $this->response->setContent($content);
        $this->response->send();
    }
}
