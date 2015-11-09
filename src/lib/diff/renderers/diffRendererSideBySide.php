<?php

namespace Karadiff\Diff\Renderers;

use Karadiff\Diff\Providers\DiffProvider;
use Karadiff\Diff\DiffSlice;

class DiffRendererSideBySide extends DiffRenderer
{
    public $lineWrapper = 'p';
    public $tokenWrapper = 'span';
    public $removalClass = 'removal';
    public $additionClass = 'addition';
    public $removalsClass = 'removals';
    public $additionsClass = 'additions';

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
                $additionsOutput[] = '+' . $this->provider->aTokens[$l];
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

    private function wrapToken($token)
    {
        $prefix = mb_substr($token, 0, 1);
        $class = '';
        switch ($prefix) {
            case '-':
                $class = $this->removalClass;
                return "<{$this->tokenWrapper} class=\"$class\">$token</{$this->tokenWrapper}>";
            case '+':
                $class = $this->additionClass;
                return "<{$this->tokenWrapper} class=\"$class\">$token</{$this->tokenWrapper}>";
            default:
                return $token;
        }
    }

    private function wrapLine($line, $num)
    {
        $class = '';
        if (mb_strpos($line, $this->removalClass) !== false) {
            $class = $this->removalsClass;
        } else if (mb_strpos($line, $this->additionClass) !== false) {
            $class = $this->additionsClass;
        }

        return "<{$this->lineWrapper} class=\"{$class}\">$num $line</{$this->lineWrapper}>";
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

            $token = trim($token);
            $lines[$l][] = $this->wrapToken($token);
        }

        $l = 0;
        $lines = array_map(function ($line) use (&$l) {
            $line = $this->wrapLine(implode(' ', $line), $l);
            $l ++;
            return $line;
        }, $lines);

        $lines = implode('', $lines);

        return $lines;
    }
}
