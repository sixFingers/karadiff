<?php

namespace Karadiff\Diff;

class Normalizer
{
    protected $normalized;

    public function __construct($string)
    {
        $this->normalized = $this->normalize($string);
    }

    public static function fromString($string)
    {
        return new Normalizer($string);
    }

    public static function fromArray($array)
    {
        $text = implode("\n", $array);
        return new Normalizer($text);
    }

    public function getText($imploded = false)
    {
        if ($imploded) {
            $lines = $this->getLines(true);
            return $this->implode($lines, true);
        }

        return $this->normalized;
    }

    public function getLines($imploded = false)
    {
        $lines = explode("\n", $this->normalized);
        $lines = array_filter($lines, function($line) {
            return $line != "";
        });

        if (!$imploded) {
            return $lines;
        }

        $last = null;
        $lines = array_filter($lines, function($line) use(&$last) {
            if (!$last) {
                $last = $line;
                return true;
            }

            if ($this->compareLines($line, $last) > .6) {
                return false;
            } else {
                $last = $line;
                return true;
            }
        });

        return array_values($lines);
    }

    protected function normalize($string)
    {
        // Check if..
        // http://stackoverflow.com/questions/10757671/how-to-remove-line-breaks-no-characters-from-the-string
        // ..is right
        // Interpret any non-interpreted entity
        $string = preg_replace_callback('/\\\[nrtvf$]/u', function($matches) {
            $entities = ['\n' => "\n", '\r' => "\r", '\t' => "\t", '\v' => "\v", '\f' => "\f", '\$' => "\$"];
            if (array_key_exists($matches[0], $entities)) {
                return $entities[$matches[0]];
            }
        }, $string);
        // Convert and collapse any tabs or spaces to single whitespace
        $string = preg_replace('/[ \t]+/u', ' ', $string);
        // Convert and collapse any line break to "\n"
        $string = preg_replace('/[\r\n]+/u', "\n", $string);
        // Strip any punctuation (pandora's box alarm)
        $string = preg_replace('/[\";:,._!?]+/', "", $string);
        $string = trim($string);

        return $string;
    }

    public function implode($lines, $imploded = false)
    {
        if ($imploded) {
            $lines = array_map(function($line) {
                return is_array($line) ?  $line[0]/* . " *" . count($line)*/: $line;
            }, $lines);
        }
        $text = trim(implode("\n", $lines));
        return $text;
    }

    public function getTokens($imploded = false)
    {
        $text = $this->getText($imploded);
        //$text = str_replace("\n", " ", $text);
        $tokens = explode(" ", $text);
        return $tokens;
    }

    protected function compareLines($a, $b)
    {
        return similar_text($a, $b) / max(mb_strlen($a), mb_strlen($b));
    }
}
