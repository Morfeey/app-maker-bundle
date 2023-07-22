<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Dto;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Enum\MethodCaseEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum as ClassType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\MethodTypeEnum as MethodType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum as ModificationType;

class MethodParametersDto
{
    public function __construct(
        protected readonly ArchitectureFileCaseDto $architectureFileCase,
        protected readonly MethodCaseEnum $case,
        protected readonly ClassType $classType,
        protected readonly ModificationType $modificationType = ModificationType::PUBLIC_,
        protected readonly MethodType $type = MethodType::NON_STATIC,
        protected readonly bool $isVirtual = false,
    ) {
    }

    public function getArchitectureFileCase(): ArchitectureFileCaseDto
    {
        return $this->architectureFileCase;
    }

    public function getCase(): MethodCaseEnum
    {
        return $this->case;
    }

    public function getClassType(): ClassType
    {
        return $this->classType;
    }

    public function getModificationType(): ModificationType
    {
        return $this->modificationType;
    }

    public function getType(): MethodType
    {
        return $this->type;
    }

    public function isVirtual(): bool
    {
        return $this->isVirtual;
    }
}
