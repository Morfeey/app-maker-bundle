<?php

namespace App\Bundles\AppMakerBundle\Infrastructure\Symfony\DependencyInjection;

use App\Bundles\InfrastructureBundle\Infrastructure\Symfony\DependencyInjection\DefaultBundleExtension;

class AppMakerBundleExtension extends DefaultBundleExtension
{
    public function getCurrentDir(): string
    {
        return __DIR__;
    }
}