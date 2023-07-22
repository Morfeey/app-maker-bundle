<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto;

interface SimpleDependencyGetterInterface
{
    public function get(): string;
}
