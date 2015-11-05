<?php

namespace Karadiff\Controllers;

use Karadiff\Helpers\TemplateHelper;
use Karadiff\Diff\Providers\DiffProviderString;
use Karadiff\Diff\Renderers\DiffRendererText;
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
            $provider = new DiffProviderString($original, $changed);
            $renderer = new DiffRendererText($provider);
            $changed = $renderer->render();
            $additions = $provider->additions;
            $removals = $provider->removals;
        }

        $content = TemplateHelper::render('welcome.html', array(
            'title' => 'Diff',
            'original' => $original,
            'changed' => $changed,
            'additions' => $additions,
            'removals' => $removals,
        ));

        $this->response->setContent($content);
        $this->response->send();
    }
}
