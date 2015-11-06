<?php

use Karadiff\Diff\Providers\DiffProvider;

class DiffTest extends PHPUnit_Framework_TestCase
{
    private $aFileLines;
    private $bFileLines;

    protected function setUp()
    {
        $this->aFileLines = ['abcde', 'mno', 'abcdx', 'fghijkl'];
        $this->bFileLines = ['mno', '765', 'abcdx', 'fghijkl'];
    }

    public function testMatchingSlices()
    {
        // two slices in common
        $aStartFirst = 1;
        $aStartSecond = 2;
        $bStartFirst = 0;
        $bStartSecond = 2;
        $aLength = count($this->aFileLines) - 1;
        $bLength = count($this->bFileLines) - 1;
        $aSliceLength = 1;
        $bSliceLength = 2;
        $provider = new DiffProvider($this->aFileLines, $this->bFileLines);
        $actual = $provider->slices;

        $this->assertEquals(3, count($actual), 'Diff slice count mismatch.');
        $this->assertEquals($aStartFirst, $actual[0]->aStart, 'File a, first slice offset mismatch.');
        $this->assertEquals($bStartFirst, $actual[0]->bStart, 'File b, first slice offset mismatch.');
        $this->assertEquals($aSliceLength, $actual[0]->length, 'First slice length mismatch.');
        $this->assertEquals($aStartSecond, $actual[1]->aStart, 'File a, second slice offset mismatch.');
        $this->assertEquals($bStartSecond, $actual[1]->bStart, 'File b, second slice offset mismatch.');
        $this->assertEquals($bSliceLength, $actual[1]->length, 'Second slice length mismatch.');
        $this->assertEquals($provider->removals, 1, 'Removals count mismatch.');
        $this->assertEquals($provider->additions, 1, 'Additions count mismatch.');
    }
}
