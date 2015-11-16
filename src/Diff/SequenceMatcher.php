<?php

namespace Karadiff\Diff;

class SequenceMatcher
{
    public $a = null;
    public $b = null;
    public $severity = null;

    public function __construct($a, $b, $severity = .9)
    {
        $this->a = $a;
        $this->b = $b;
        $this->severity = $severity;
    }

    public function findLongestMatch($a0 = 0, $a1 = 0, $b0 = 0, $b1 = 0)
    {
        // Start offset accumulators
        $sa = $a0;
        $sb = $b0;
        // LCS Length accumulator
        $n = 0;
        // Runs buffer
        $runs = [];
        // Run through each token in array a
        for ($i = $a0; $i < $a1; $i ++) {
            $newRuns = [];
            // Run through each token in array b
            for ($j = $b0; $j < $b1; $j ++) {
                // If we're getting a correspondence...
                if ($this->compareTokens($this->a[$i], $this->b[$j]) > $this->severity) {
                    // Set a new start from 0, or last offset
                    $v = isset($runs[$j - 1]) ? $runs[$j - 1]: 0;
                    // Increment the current run length by 1
                    $k = $newRuns[$j] = $v + 1;
                    // If current run length is greater than
                    // previous' greatest one, swap the accumulators
                    if ($k > $n) {
                        $sa = $i - $k + 1;
                        $sb = $j - $k + 1;
                        $n = $k;
                    }
                }
            }
            // Swap runs buffer
            $runs = $newRuns;
        }

        return [$sa, $sb, $n];
    }

    public function getMatchingBlocks()
    {
        $la = count($this->a);
        $lb = count($this->b);
        $queue = [[0, $la, 0, $lb]];
        $matchingBlocks = [];

        while (!empty($queue)) {
            list($a0, $a1, $b0, $b1) = array_pop($queue);
            list($i, $j, $k) = $x = $this->findLongestMatch($a0, $a1, $b0, $b1);
            if ($k != 0) {
                $matchingBlocks[] = $x;

                if ($a0 < $i && $b0 < $j) {
                    $queue[] = [$a0, $i, $b0, $j];
                }

                if ($i + $k < $a1 && $j + $k < $b1) {
                    $queue[] = [$i + $k, $a1, $j + $k, $b1];
                }
            }
        }

        sort($matchingBlocks);

        $i1 = $j1 = $k1 = 0;
        $nonAdjacent = [];
        foreach ($matchingBlocks as $matchingBlock) {
            list($i2, $j2, $k2) = $matchingBlock;

            if ($i1 + $k1 == $i2 && $j1 + $k1 == $j2) {
                $k1 += $k2;
            } else {
                if ($k1) {
                    $nonAdjacent[] = [$i1, $j1, $k1];
                }

                list($i1, $j1, $k1) = [$i2, $j2, $k2];
            }
        }

        if ($k1) {
            $nonAdjacent[] = [$i1, $j1, $k1];
        }

        $nonAdjacent[] = [$la, $lb, 0];

        return $nonAdjacent;
    }

    public function ratio()
    {
        $matches = array_reduce($this->getMatchingBlocks(), function($a, $b) {
            $a += $b[2];
            return $a;
        });

        return $this->calculateRatio($matches, count(array_merge($this->a, $this->b)));
    }

    public function getOpCodes($withTokens = false)
    {
        $i = $j = 0;
        $answer = [];

        foreach ($this->getMatchingBlocks() as $matchingBlock) {
            list($ai, $bj, $size) = $matchingBlock;
            $tag = false;
            if ($i < $ai && $j < $bj) {
                $tag = 'replace';
            } else if ($i < $ai) {
                $tag = 'delete';
            } else if ($j < $bj) {
                $tag = 'insert';
            }

            if ($tag) {
                $op = [$tag, $i, $ai, $j, $bj];
                if ($withTokens) {
                    $op[] = array_slice($this->a, $i, $ai - $i);
                    $op[] = array_slice($this->b, $j, $bj - $j);
                }
                array_push($answer, $op);
            }

            $i = $ai + $size;
            $j = $bj + $size;

            if ($size) {
                $op = ['equal', $ai, $i, $bj, $j];
                if ($withTokens) {
                    $op[] = array_slice($this->a, $ai, $i - $ai);
                    $op[] = array_slice($this->b, $bj, $j - $bj);
                }
                array_push($answer, $op);
            }
        }

        return $answer;
    }

    public function getGroupedOpCodes($n = 3)
    {
        $codes = $this->getOpCodes();
        if (empty($codes)) {
            $codes = [['equal', 0, 1, 0, 1]];
        }

        if ($codes[0][0] == 'equal') {
            list($tag, $i1, $i2, $j1, $j2) = $codes[0];
            $codes[0] = [$tag, max($i1, $i2 - $n), $i2, max($j1, $j2 - $n), $j2];
        }

        $last = count($codes) > 0 ? count($codes) - 1: 0;
        if ($codes[$last][0] == 'equal') {
            list($tag, $i1, $i2, $j1, $j2) = $codes[$last];
            $codes[$last] = [$tag, $i1, min($i2, $i1 + $n), $j1, min($j2, $j1 + $n)];
        }

        $nn = $n + $n;
        $group = [];
        foreach ($codes as $code) {
            list($tag, $i1, $i2, $j1, $j2) = $code;
            if ($tag == 'equal' && $i2 - $i1 > $nn) {
                $group[] = [$tag, $i1, min($i2, $i1 + $n), $j1, min($j2, $j1 +$n)];
                yield $group;
                $group = [];
                $i1 = max($i1, $i2 - $n);
                $j1 = max($j1, $j2 - $n);
            }

            $group[] = [$tag, $i1, $i2, $j1, $j2];
        }

        if (!empty($group) && !(count($group) == 1 && $group[0][0] == 'equal')) {
            yield $group;
        }
    }

    public function compareTokens($a, $b)
    {
        $a = strtolower($a);
        $b = strtolower($b);

        if (strcmp($a, $b) == true)
            return true;

        if (mb_strlen($a) == 0 || mb_strlen($b) == 0) {
            return false;
        }

        return similar_text($a, $b) / max(mb_strlen($a), mb_strlen($b));
    }

    protected function calculateRatio($matches, $length)
    {
        if ($length) {
            return 2.0 * $matches / $length;
        }

        return 1.0;
    }
}
