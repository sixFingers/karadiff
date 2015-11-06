<?php

namespace Karadiff\Diff\Providers;

use Karadiff\Diff\Diff;

class DiffProviderStringWord extends DiffProvider
{
    protected function splitIntoTokens() {
        $this->aContent = trim($this->aContent);
        $this->bContent = trim($this->bContent);
        // Converty _any_ break to white space, without breaking UTF8 encoding
        $this->aContent = trim(preg_replace('/[\p{Z}\s]+/u', ' ', $this->aContent));
        $this->bContent = trim(preg_replace('/[\p{Z}\s]+/u', ' ', $this->bContent));

        $this->aTokens = preg_split('/ /', $this->aContent);
        $this->bTokens = preg_split('/ /', $this->bContent);
    }
}
