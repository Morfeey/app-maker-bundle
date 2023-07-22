<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Service;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\StringValuePrototypeTrait;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\UseCreatorService;
use ReflectionProperty;

class GetterService
{
    use StringValuePrototypeTrait;

    public function __construct(
        protected readonly UseCreatorService $useCreatorService
    ) {
    }

    public function createNameByProperty(ReflectionProperty $property): string
    {
        return $this->createName($property->getName(), $property->getType()?->allowsNull());
    }

    public function createName(string $propertyName, bool $isAllowsNull = false): string
    {
        $stringPropertyName = $this->createPrototypeStringValue()->setString($propertyName)->firstCharUp();
        if ($stringPropertyName->startsWith('Is') && $isAllowsNull) {
            return 'get' . $stringPropertyName->firstCharLow()->getResult();
        }

        if ($stringPropertyName->startsWith('Is')) {
            return $stringPropertyName->firstCharLow()->getResult();
        }

        return
            $stringPropertyName
                ->setString('get' . $stringPropertyName->getResult())
                ->toCamelCase()
                ->firstCharLow()
                ->getResult();
    }

    public function createUseByProperty(ReflectionProperty $property): ?UseNamespaceDto
    {
        return $this->useCreatorService->createByProperty($property);
    }

    public function createTypehintByProperty(ReflectionProperty $property): string
    {
        return ($property->getType()?->allowsNull() ? '?': '') . $property->getType()?->getName();
    }

    public function createContentByProperty(ReflectionProperty $property): string
    {
        return $this->createContentByPropertyName($property->getName());
    }

    public function createContentByPropertyName(string $propertyName): string
    {
        return "return \$this->{$propertyName};";
    }
}
