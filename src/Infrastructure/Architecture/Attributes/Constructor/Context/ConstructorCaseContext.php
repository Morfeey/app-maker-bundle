<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Constructor\Context;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Constructor\Cases\ConstructorCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Constructor\Enum\ConstructorTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ConstructorDto as Constructor;

class ConstructorCaseContext
{
    public function __construct(protected readonly iterable $constructorCases)
    {
    }

    public function create(ConstructorTypeEnum $type, ArchitectureFileCaseDto $caseParameters): ?Constructor
    {
        /** @var ConstructorCaseInterface $case */
        foreach ($this->constructorCases as $case) {
            if ($case->isCanCreate($type, $caseParameters)) {
                return $case->create($caseParameters, $type);
            }
        }

        return null;
    }
}
