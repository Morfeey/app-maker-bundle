<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\NamespaceHelper;
use ReflectionProperty;

class UseCreatorService
{
    public const SIMPLE_TYPES = ['int', 'bool', 'string', 'float', 'null', 'mixed'];

    use NamespaceHelper;
    public function createByProperty(ReflectionProperty $property): ?UseNamespaceDto
    {
        if (!$property->getType() || in_array($property->getType()->getName(), self::SIMPLE_TYPES)) {
            return null;
        }

        return new UseNamespaceDto(
            $property->getType()->getName(),
            $this->getClassNameByNamespace($property->getType()->getName())
        );
    }

    public function create(string $namespace, string $class, ?string $alias = null, bool $isArray = false): UseNamespaceDto
    {
        return new UseNamespaceDto($namespace, $class, $alias, $isArray);
    }
}
