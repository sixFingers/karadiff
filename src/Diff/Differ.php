<?php

namespace Karadiff\Diff;

use Karadiff\Diff\Normalizer;
use Karadiff\Diff\SequenceMatcher;

class Differ
{
    private $a;
    private $b;
    private $ops;
    public $additions;
    public $removals;

    public function __construct($a, $b)
    {
        $this->a = $a;
        $this->b = $b;
        $this->precalculate();
    }

    private function precalculate()
    {
        $an = Normalizer::fromString($this->a);
        $bn = Normalizer::fromString($this->b);
        $al = $an->getLines(true);
        $bl = $bn->getLines(true);
        $slm = new SequenceMatcher($al, $bl, .7);
        $lops = $slm->getOpCodes(true);
        $ops = [];
        foreach ($lops as $lop) {
            $atn = Normalizer::fromArray($lop[5]);
            $btn = Normalizer::fromArray($lop[6]);
            $at = $atn->getTokens();
            $bt = $btn->getTokens();
            $swm = new SequenceMatcher($at, $bt, .9);
            $wops = $swm->getOpCodes(true);
            $op = $lop;
            $op['wops'] = $wops;
            $ops[] = $op;
        }

        $this->ops = $ops;
        foreach ($this->ops as $o => $lop) {
            foreach ($lop['wops'] as $wop) {
                list($wtag, $as, $al, $bs, $bl, $at, $bt) = $wop;
                switch ($wop[0]) {
                    case 'delete':
                        $this->removals += count($at);
                        break;
                    case 'insert':
                        $this->additions += count($bt);

                        break;
                    case 'replace':
                        $this->removals += count($at);
                        $this->additions += count($bt);
                        break;
                }
            }
        }
    }

    public function render()
    {
        $output = '';
        foreach ($this->ops as $o => $lop) {
            $output .= $this->renderLop($lop);
        }

        return $output;
    }

    public function renderLop($lop)
    {
        list($ltag, $as, $al, $bs, $bl, $at, $bt) = $lop;
        $output = '';
        $wops = $lop['wops'];

        $rowLeftClass = '';
        $rowRightClass = '';
        if ($ltag == "remove") {
            $rowLeftClass = 'removal';
        }

        if ($ltag == "replace") {
            $rowLeftClass = 'removal';
            $rowRightClass = 'addition';
        }

        if ($ltag == "insert") {
            $rowRightClass = 'addition';
        }

        $output .= '<tr>';
        $output .= "<td class=\"$rowLeftClass\">";
        foreach ($wops as $o => $wop) {
            $output .= " ";
            $output .= $this->renderWop($wop);
        }
        $output .= "</td>";
        $output .= "<td class=\"$rowRightClass\">";
        foreach ($wops as $o => $wop) {
            $output .= " ";
            $output .= $this->renderWop($wop, false);
        }
        $output .= "</td>";
        $output .= '</tr>';

        return $output;
    }

    public function renderWop($wop, $isLeft = true)
    {
        list($wtag, $as, $al, $bs, $bl, $at, $bt) = $wop;
        $output = '';

        if ($isLeft) {
            if ($wtag == 'equal') {
                $content = str_replace("\n", "<br>", implode(" ", $wop[5]));
                $output .= $content;
            } elseif ($wtag == 'delete' || $wtag == 'replace') {
                $content = str_replace("\n", "<br>", implode(" ", $wop[5]));
                $output .= '<del>' . $content . '</del>';
            }
        } else {
            if ($wtag == 'equal') {
                $content = str_replace("\n", "<br>", implode(" ", $wop[5]));
                $output .= $content;
            } elseif ($wtag == 'insert' || $wtag == 'replace') {
                $content = str_replace("\n", "<br>", implode(" ", $wop[6]));
                $output .= '<ins>' . $content . '</ins>';
            }
        }

        return $output;
    }
}
