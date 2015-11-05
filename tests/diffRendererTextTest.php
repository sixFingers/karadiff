<?php

use Karadiff\Diff\Providers\DiffProviderFile;
use Karadiff\Diff\Renderers\DiffRendererText;

class DiffRendererTextTest extends PHPUnit_Framework_TestCase
{
    private $cFile;

    public function setUp()
    {
        $this->cFile = file_get_contents('tests/mocks/c.txt');
    }

    public function testRenderer()
    {
        $aFile = 'tests/mocks/a.txt';
        $bFile = 'tests/mocks/b.txt';
        $provider = new DiffProviderFile($aFile, $bFile);
        $renderer = new DiffRendererText($provider);
        $actual = $renderer->render();
        $this->assertEquals($this->cFile, $actual, 'Diff output mismatch.');
    }
}
