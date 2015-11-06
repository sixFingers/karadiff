<?php

namespace Karadiff\Controllers;

use Karadiff\Helpers\TemplateHelper;
use Karadiff\Diff\Providers\DiffProviderStringWord;
use Karadiff\Diff\Renderers\DiffRendererSideBySide;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function index()
    {
        $sides = ['', ''];
        $additionsCount = $removalsCount = 0;

        if ($this->request->isMethod(Request::METHOD_POST)) {
            $original = $this->request->request->get('original', '');
            $changed = $this->request->request->get('changed', '');
            $provider = new DiffProviderStringWord($original, $changed);
            $renderer = new DiffRendererSideBySide($provider);
            $sides = $renderer->render();
            $additionsCount = $provider->additions;
            $removalsCount = $provider->removals;
        }

        $content = TemplateHelper::render('welcome.html', array(
            'title' => 'Diff',
            'sides' => $sides,
            'additionsCount' => $additionsCount,
            'removalsCount' => $removalsCount,
        ));

        $this->response->setContent($content);
        $this->response->send();
    }
}
