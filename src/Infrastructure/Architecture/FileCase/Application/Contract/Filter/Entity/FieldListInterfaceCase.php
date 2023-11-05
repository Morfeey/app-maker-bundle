<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Filter\Entity;

use App\Bundles\InfrastructureBundle\Application\Contract\Filter\FieldList\ContractEntityFieldListInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\MethodTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\CreatorDefaultArchitectureFileDtoHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;

class FieldListInterfaceCase implements ArchitectureFileCaseInterface
{

    use CreatorDefaultArchitectureFileDtoHelper, PrototypeHelper;
    public function __construct(
        private readonly AttributesCreatorFacade $attributesCreatorFacade
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        $methods = $this->createCollection();
        foreach ($caseParameters->getReflectionEntity()->getProperties() as $property) {
            $methods->add(
                $this->attributesCreatorFacade->createMethod(
                    $property->getName(),
                    'static',
                    $this->createCollection(),
                    MethodTypeEnum::NON_STATIC,
                    ModificationTypeEnum::PUBLIC_,
                    true
                )
            );
        }

        return
            $this->createDefault($caseParameters)
                ->setClassType(ClassTypeEnum::INTERFACE_)
                ->setMethodCollection($methods)
                ->setExtendsCollection(
                    $this->createCollection()
                        ->add($this->attributesCreatorFacade->createUse(ContractEntityFieldListInterface::class))
                );
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto
    {
        $fileCase = $this->attributesCreatorFacade->createCaseDtoByCase($fileCase, $this);

        return $this->attributesCreatorFacade->createUse($fileCase->getCaseDto()->getNamespace(), null, null, $isArray);
    }
}
