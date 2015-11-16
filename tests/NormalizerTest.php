<?php

use Karadiff\Diff\Normalizer;

class NormalizerTest extends PHPUnit_Framework_TestCase
{
    public function testNormalize()
    {
        $content = @file_get_contents('./tests/mocks/toBeNormalized.txt');
        $normalizer = Normalizer::fromString($content);

        $normalized = $normalizer->getText();
        $lineBreaks = mb_substr_count($normalized, "\n");
        $spaces = mb_substr_count($normalized, " ");
        $tabs = mb_substr_count($normalized, "\t");
        $lines = count($normalizer->getLines());

        $this->assertEquals(6, $lineBreaks);
        $this->assertEquals(33, $spaces);
        $this->assertEquals(0, $tabs);
        $this->assertEquals(7, $lines);
    }

    public function testGroupedNormalize()
    {
        $content = @file_get_contents('./tests/mocks/toBeLineBlocked.txt');
        $normalizer = Normalizer::fromString($content);

        $expected = @file_get_contents('./tests/mocks/alreadyLineBlocked.txt');
        $expected = rtrim($expected);
        $this->assertEquals($expected, $normalizer->getText(true));
    }
}
