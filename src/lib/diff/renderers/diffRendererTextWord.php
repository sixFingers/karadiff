<?php

namespace Karadiff\Diff\Renderers;

use Karadiff\Diff\Providers\DiffProvider;
use Karadiff\Diff\DiffSlice;

class DiffRendererTextWord extends DiffRenderer
{
    public function render()
    {
        $slices = $this->provider->slices;
        // Ensure we cover everything until the end of b content
        $endSlice = new DiffSlice(count($this->provider->aTokens), count($this->provider->bTokens), 0);
        array_push($slices, $endSlice);

        $ia = $ib = 0;
        $output = [];

        foreach ($slices as $slice) {
            for ($l = $ia; $l < $slice->aStart; $l ++) {
                $output[] = [
                    't' => 'removal',
                    'content' => $this->provider->aTokens[$l]
                ];
            }

            for ($l = $ib; $l < $slice->bStart; $l ++) {
                $output[] = [
                    't' => 'addition',
                    'content' => $this->provider->bTokens[$l]
                ];
            }

            for ($l = $slice->aStart; $l < $slice->aStart + $slice->length; $l ++) {
                $output[] = [
                    't' => '',
                    'content' => $this->provider->aTokens[$l]
                ];
            }

            $ia = $slice->aStart + $slice->length;
            $ib = $slice->bStart + $slice->length;
        }

        $lines = [];
        $l = -1;
        foreach ($output as $token) {
            $isUpperCase = ctype_upper(mb_substr($token['content'], 0, 1));
            $isNewLine = $isUpperCase || $l == -1;

            if ($isNewLine) {
                $l ++;
            }

            $content = $this->wrapToken($token);
            $lines[$l][] = $content;
        }

        $lines = array_map(function ($line) {
            return implode(' ', $line);
        }, $lines);
        $lines = implode("\n", $lines);

        return $lines;
    }

    private function wrapToken($token)
    {
        if ($token['t'] == '') {
            return $token['content'];
        }

        return "<span class=\"{$token['t']}\">{$token['content']}</span>";
    }
}
