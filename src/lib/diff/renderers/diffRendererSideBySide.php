<?php

namespace Karadiff\Diff\Renderers;

use Karadiff\Diff\Providers\DiffProvider;
use Karadiff\Diff\DiffSlice;

class DiffRendererSideBySide extends DiffRenderer
{
    public function render()
    {
        $slices = $this->provider->slices;
        // Ensure we cover everything until the end of b content
        $endSlice = new DiffSlice(count($this->provider->aTokens), count($this->provider->bTokens), 0);
        array_push($slices, $endSlice);

        $ia = $ib = 0;
        $removalsOutput = [];
        $additionsOutput = [];
        $commonOutput = [];

        foreach ($slices as $slice) {
            for ($l = $ia; $l < $slice->aStart; $l ++) {
                $removalsOutput[] = '-' . $this->provider->aTokens[$l];
            }

            for ($l = $ib; $l < $slice->bStart; $l ++) {
                $additionsOutput[] = '+' . $this->provider->bTokens[$l];
            }

            for ($l = $slice->aStart; $l < $slice->aStart + $slice->length; $l ++) {
                $removalsOutput[] = $additionsOutput[] = ' ' . $this->provider->aTokens[$l];
            }

            $ia = $slice->aStart + $slice->length;
            $ib = $slice->bStart + $slice->length;
        }

        $removals = $this->format($removalsOutput);
        $additions = $this->format($additionsOutput);

        return [
            'removals' => $removals,
            'additions' => $additions
        ];
    }

    private function format($output)
    {
        $lines = [];
        $l = -1;

        foreach ($output as $token) {
            $isUpperCase = ctype_upper(mb_substr($token, 1, 1));
            $isNewLine = $isUpperCase || $l == -1;

            if ($isNewLine) {
                $l ++;
            }

            $lines[$l][] = trim($token);
        }

        $lines = array_map(function ($line) {
            return implode(' ', $line);
        }, $lines);
        $lines = implode("\n", $lines);

        return $lines;
    }
}
