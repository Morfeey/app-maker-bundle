<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Cases;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Cases\MethodCaseInterface;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Dto\MethodParametersDto as MethodParameters;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Enum\MethodCaseEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Service\GetterService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\MethodCreatorService;

class GetterCase implements MethodCaseInterface
{

    use PrototypeHelper;
    public function __construct(
        protected readonly GetterService $getterService,
        protected readonly MethodCreatorService $methodCreatorService
    ) {
    }

    public function isCanCreate(MethodParameters $parameters): bool
    {
        return
            in_array($parameters->getCase(), [MethodCaseEnum::GETTER, MethodCaseEnum::GETTER_AND_NULLABLE_ID])
            && ClassTypeEnum::INTERFACE_ !== $parameters->getClassType();
    }

    public function create(MethodParameters $parameters): CollectionInterface
    {
        $methods = $this->createCollection();
        foreach ($parameters->getArchitectureFileCase()->getReflectionEntity()->getProperties() as $property) {
            $typeHint = $this->getterService->createTypehintByProperty($property);
            if ($parameters->getCase() === MethodCaseEnum::GETTER_AND_NULLABLE_ID && $property->getName() === 'id') {
                $typeHint = '?' . $property->getType()?->getName();
            }

            $methods->add(
                $this->methodCreatorService->create(
                    $this->createCollection(),
                    $typeHint,
                    $this->getterService->createNameByProperty($property),
                    $parameters->getType(),
                    $parameters->getModificationType(),
                    false,
                    $this->getterService->createContentByProperty($property),
                    $parameters->getCase(),
                    $this->getterService->createUseByProperty($property)
                )
            );
        }

        return $methods;
    }
}
