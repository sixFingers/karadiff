<?php

namespace Karadiff\Diff\Renderers;

use Karadiff\Diff\Providers\DiffProvider;

class DiffRenderer
{
    protected $provider;

    public function __construct(DiffProvider $provider)
    {
        $this->provider = $provider;
    }

    public function render()
    {
    }
}
