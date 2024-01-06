<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture;

interface DefaultArchitectureCreatorByDoctrineEntityClassInterface
{
    public function create(string $entityClassName, bool $isDisableOverride): void;
}
