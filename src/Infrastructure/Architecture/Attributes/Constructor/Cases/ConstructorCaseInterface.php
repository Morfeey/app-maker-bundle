<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Constructor\Cases;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Constructor\Enum\ConstructorTypeEnum as ConstructorType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as ArchitectureFileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ConstructorDto as Constructor;

interface ConstructorCaseInterface
{
    public function isCanCreate(ConstructorType $type, ArchitectureFileCase $caseParameters): bool;
    public function create(ArchitectureFileCase $caseParameters, ConstructorType $constructorTypeEnum): Constructor;
}
