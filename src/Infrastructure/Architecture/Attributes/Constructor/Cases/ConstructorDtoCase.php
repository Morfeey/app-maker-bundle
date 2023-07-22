<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Constructor\Cases;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Constructor\Cases\ConstructorCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Constructor\Enum\ConstructorTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Constructor\Enum\ConstructorTypeEnum as ConstructorType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as ArchitectureFileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ConstructorDto as Constructor;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\ConstructorCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\DefaultValueCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\TypeCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\UseCreatorService;

class ConstructorDtoCase implements ConstructorCaseInterface
{

    public function __construct(
        protected readonly ConstructorCreatorService $constructorCreatorService,
        protected readonly AttributesCreatorFacade $attributesCreatorFacade,
        protected readonly TypeCreatorService $typeCreatorService,
        protected readonly UseCreatorService $useCreatorService,
        protected readonly DefaultValueCreatorService $defaultValueCreatorService
    ) {
    }

    public function isCanCreate(ConstructorTypeEnum $type, ArchitectureFileCase $caseParameters): bool
    {
        return in_array($type, [ConstructorType::DTO, ConstructorType::DTO_WITH_NULLABLE_ID]);
    }

    public function create(ArchitectureFileCase $caseParameters, ConstructorTypeEnum $constructorTypeEnum): Constructor
    {
        $constructorParameters = [];
        foreach ($caseParameters->getReflectionEntity()->getProperties() as $property) {
            $defaultValue = null;
            if ($property->hasDefaultValue()) {
                $defaultValue = $this->defaultValueCreatorService->create($property->getDefaultValue());
            }

            $type = $property->getType()?->allowsNull() ? '?' . $property->getType()?->getName() : $property->getType()?->getName();
            if ($constructorTypeEnum === ConstructorTypeEnum::DTO_WITH_NULLABLE_ID && $property->getName() === 'id') {
                $type = '?' . $property->getType()?->getName();
            }

            $constructorParameters[] = $this->attributesCreatorFacade->createParameter(
                $property->getName(),
                $this->typeCreatorService->create(
                    false,
                    false,
                    $type,
                    $this->useCreatorService->createByProperty($property)
                ),
                $defaultValue,
                ModificationTypeEnum::PRIVATE_,
                true
            );
        }

        return $this->constructorCreatorService->create(null, ...$constructorParameters);
    }
}
