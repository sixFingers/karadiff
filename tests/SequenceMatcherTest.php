<?php

use Karadiff\Diff\Normalizer;
use Karadiff\Diff\SequenceMatcher;

class SequenceMatcherTest extends PHPUnit_Framework_TestCase
{
    public function testItWorks()
    {
        $a = @file_get_contents('./tests/mocks/toBeComparedA.txt');
        $b = @file_get_contents('./tests/mocks/toBeComparedB.txt');
        $an = new Normalizer($a);
        $bn = new Normalizer($b);
        $al = $an->getLines();
        $bl = $bn->getLines();
        $at = $an->getTokens();
        $bt = $bn->getTokens();
        $slm = new SequenceMatcher($al, $bl);
    }
}
