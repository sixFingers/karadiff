<?php

namespace Karadiff\Diff;

class DiffSlice
{
    public $aStart;
    public $bStart;
    public $length;

    public function __construct($aStart = 0, $bStart = 0, $length = 0)
    {
        $this->aStart = $aStart;
        $this->bStart = $bStart;
        $this->length = $length;
    }
}
