<?php

namespace Karadiff\Diff\Providers;

use Karadiff\Diff\Diff;

class DiffProviderString extends DiffProvider
{
    protected function splitIntoTokens()
    {
        $this->aContent = trim($this->aContent);
        $this->bContent = trim($this->bContent);
        $this->aTokens = preg_split('/\r\n|\n|\r/', $this->aContent);
        $this->bTokens = preg_split('/\r\n|\n|\r/', $this->bContent);
    }
}
