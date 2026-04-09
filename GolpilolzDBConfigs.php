<?php

namespace Golpilolz\DBConfigs;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class GolpilolzDBConfigs extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
