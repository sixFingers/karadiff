<?php

namespace Karadiff\Diff\Providers;

use Karadiff\Diff\DiffSlice;

class DiffProvider
{
    protected $aContent;
    protected $bContent;
    public $aTokens;
    public $bTokens;
    public $removals = 0;
    public $additions = 0;
    public $slices;

    public function __construct($aContent, $bContent)
    {
        $this->aContent = $aContent;
        $this->bContent = $bContent;

        $this->splitIntoTokens();
        $this->calculate();
    }

    protected function splitIntoTokens()
    {
        $this->aTokens = $this->aContent;
        $this->bTokens = $this->bContent;
    }

    protected function calculate()
    {
        $slices = $this->matchingSlices(0, count($this->aTokens), 0, count($this->bTokens));
        // Ensure we cover everything until the end of b content (just for counting purposes)
        $endSlice = new DiffSlice(count($this->aTokens), count($this->bTokens), 0);
        array_push($slices, $endSlice);

        $ia = $ib = 0;

        foreach ($slices as $slice) {
            for ($l = $ia; $l < $slice->aStart; $l ++) {
                $this->removals ++;
            }

            for ($l = $ib; $l < $slice->bStart; $l ++) {
                $this->additions ++;
            }

            $ia = $slice->aStart + $slice->length;
            $ib = $slice->bStart + $slice->length;
        }

        $this->slices = $slices;
    }

    protected function longestCommonSlice($a0, $a1, $b0, $b1)
    {
        // Start offset accumulators
        $sa = $a0;
        $sb = $b0;
        // LCS Length accumulator
        $n = 0;

        // Runs buffer
        $runs = [];
        // Run through each line in file a
        for ($i = $a0; $i < $a1; $i ++) {
            $newRuns = [];

            // Run through each line in file b
            for ($j = $b0; $j < $b1; $j ++) {
                // If we're getting a correspondence...
                if ($this->aTokens[$i] == $this->bTokens[$j]) {
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

        return new DiffSlice($sa, $sb, $n);
    }

    protected function matchingSlices($a0, $a1, $b0, $b1)
    {
        $lcs = $this->longestCommonSlice($a0, $a1, $b0, $b1);
        $sa = $lcs->aStart;
        $sb = $lcs->bStart;
        $n = $lcs->length;

        if ($n == 0) {
            return [];
        }

        return array_merge(
            $this->matchingSlices($a0, $sa, $b0, $sb),
            [$lcs],
            $this->matchingSlices($sa + $n, $a1, $sb + $n, $b1)
        );
    }
}
