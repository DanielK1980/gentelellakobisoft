<?php

namespace Infogold\StronaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class InfogoldStronaBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
