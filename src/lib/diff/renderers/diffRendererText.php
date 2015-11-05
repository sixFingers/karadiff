<?php

namespace Karadiff\Diff\Renderers;

use Karadiff\Diff\Providers\DiffProvider;
use Karadiff\Diff\DiffSlice;

class DiffRendererText extends DiffRenderer
{
    public function render()
    {
        $slices = $this->provider->slices;
        // Ensure we cover everything until the end of b content
        $endSlice = new DiffSlice(count($this->provider->aTokens), count($this->provider->bTokens), 0);
        array_push($slices, $endSlice);

        $ia = $ib = 0;
        $output = '';

        foreach($slices as $slice) {
            for($l = $ia; $l < $slice->aStart; $l ++) {
                $output .= "- {$this->provider->aTokens[$l]}\n";
            }

            for($l = $ib; $l < $slice->bStart; $l ++) {
                $output .= "+ {$this->provider->bTokens[$l]}\n";
            }

            for($l = $slice->aStart; $l < $slice->aStart + $slice->length; $l ++) {
                $output .= "  {$this->provider->aTokens[$l]}\n";
            }

            $ia = $slice->aStart + $slice->length;
            $ib = $slice->bStart + $slice->length;
        }

        return $output;
    }
}
