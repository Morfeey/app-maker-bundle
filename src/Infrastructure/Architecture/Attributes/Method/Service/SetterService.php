<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Service;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\TypeDto as Type;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\NamespaceHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\TypeCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\UseCreatorService;
use ReflectionProperty;

class SetterService
{
    use PrototypeHelper, NamespaceHelper;
    public function __construct(
        protected readonly UseCreatorService $useCreatorService,
        protected readonly TypeCreatorService $typeCreatorService
    ) {
    }

    public function createTypehint(): string
    {
        return 'static';
    }

    public function createNameByProperty(ReflectionProperty $property): string
    {
        return $this->createName($property->getName());
    }

    public function createName(string $propertyName): string
    {
        return
            'set' . $this->createPrototypeStringValue()
                ->setString($propertyName)
                ->toCamelCase()
                ->firstCharUp()
                ->getResult();
    }

    public function createTypeByProperty(ReflectionProperty $property): Type
    {
        $type = $property->getType();
        $use = null;
        if ($type && !in_array($type->getName(), UseCreatorService::SIMPLE_TYPES)) {
            $use = $this->useCreatorService->create($type->getName(), $this->getClassNameByNamespace($type->getName()));
        }

        return $this->typeCreatorService->create(
            false,
            false,
            $type->getName(),
            $use
        );
    }
}
