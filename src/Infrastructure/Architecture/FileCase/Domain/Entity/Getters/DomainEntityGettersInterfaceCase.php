<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Entity\Getters;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Enum\MethodCaseEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\MethodTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Entity\DefaultDomainEntityInterfaceCase;

class DomainEntityGettersInterfaceCase extends DefaultDomainEntityInterfaceCase implements ArchitectureFileCaseInterface
{
    public function create(FileCase $caseParameters): ArchitectureFile
    {
        $getters = $this->attributesCreator->createMethods(
            $this->attributesCreator->createMethodParameters(
            $caseParameters,
            MethodCaseEnum::GETTER,
            ClassTypeEnum::INTERFACE_,
            ModificationTypeEnum::PUBLIC_,
            MethodTypeEnum::NON_STATIC,
            true
            )
        );

        return parent::create($caseParameters)->setMethodCollection($getters);
    }
}
