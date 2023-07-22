<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Cases;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Cases\MethodCaseInterface;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Dto\MethodParametersDto as MethodParameters;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Enum\MethodCaseEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Service\SetterService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\DefaultValueCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\MethodCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\ParameterCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\UseCreatorService;

class SetterInterfaceCase implements MethodCaseInterface
{

    use PrototypeHelper;
    public function __construct(
        protected readonly SetterService $setterService,
        protected readonly MethodCreatorService $methodCreatorService,
        protected readonly ParameterCreatorService $parameterCreatorService,
        protected readonly DefaultValueCreatorService $defaultValueCreatorService,
        protected readonly UseCreatorService $useCreatorService
    ) {
    }

    public function isCanCreate(MethodParameters $parameters): bool
    {
        return
            MethodCaseEnum::SETTER === $parameters->getCase()
            && ClassTypeEnum::INTERFACE_ === $parameters->getClassType();
    }

    public function create(MethodParameters $parameters): CollectionInterface
    {
        $methods = $this->createCollection();
        foreach ($parameters->getArchitectureFileCase()->getReflectionEntity()->getProperties() as $property) {
            $parameterCollection = $this->createCollection();
            $defaultValue = null;
            if ($property->hasDefaultValue()) {
                $defaultValue = $this->defaultValueCreatorService->create($property->getDefaultValue());
            }

            $parameter = $this->parameterCreatorService->create(
                $property->getName(),
                $this->setterService->createTypeByProperty($property),
                $defaultValue
            );

            $methods->add(
                $this->methodCreatorService->create(
                    $parameterCollection->add($parameter),
                    $this->setterService->createTypehint(),
                    $this->setterService->createNameByProperty($property),
                    $parameters->getType(),
                    $parameters->getModificationType(),
                    true,
                    null,
                    $parameters->getCase()
                )
            );
        }

        return $methods;
    }
}
