<?php

namespace App\Bundles\AppMakerBundle\Infrastructure\Symfony;

use App\Bundles\AppMakerBundle\Infrastructure\Symfony\DependencyInjection\AppMakerBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppMakerBundle extends Bundle
{
    public function getPath(): string
    {
        return __DIR__;
    }

    public function getContainerExtension(): AppMakerBundleExtension
    {
        return new AppMakerBundleExtension();
    }
}