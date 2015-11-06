<?php

use Karadiff\Diff\Providers\DiffProviderFile;

class DiffProviderFileTest extends PHPUnit_Framework_TestCase
{
    private $aFileLines;
    private $bFileLines;
    private $cFile;

    protected function setUp()
    {
        $this->aFileLines = ['abcde', 'mno', 'abcdx', 'fghijkl'];
        $this->bFileLines = ['mno', '765', 'abcdx', 'fghijkl'];
    }

    public function testGet()
    {
        $aFile = 'tests/mocks/a.txt';
        $bFile = 'tests/mocks/b.txt';
        $provider = new DiffProviderFile($aFile, $bFile);
        $aStartFirst = 1;
        $aStartSecond = 2;
        $bStartFirst = 0;
        $bStartSecond = 2;
        $aLength = count($this->aFileLines) - 1;
        $bLength = count($this->bFileLines) - 1;
        $aSliceLength = 1;
        $bSliceLength = 2;
        $actual = $provider->slices;

        $this->assertEquals(3, count($actual), 'Diff slice count mismatch.');
        $this->assertEquals($aStartFirst, $actual[0]->aStart, 'File a, first slice offset mismatch.');
        $this->assertEquals($bStartFirst, $actual[0]->bStart, 'File b, first slice offset mismatch.');
        $this->assertEquals($aSliceLength, $actual[0]->length, 'First slice length mismatch.');
        $this->assertEquals($aStartSecond, $actual[1]->aStart, 'File a, second slice offset mismatch.');
        $this->assertEquals($bStartSecond, $actual[1]->bStart, 'File b, second slice offset mismatch.');
        $this->assertEquals($bSliceLength, $actual[1]->length, 'Second slice Length mismatch.');
    }
}
