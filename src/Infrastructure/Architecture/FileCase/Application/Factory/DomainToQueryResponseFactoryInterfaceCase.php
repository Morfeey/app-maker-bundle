<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Factory;

use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\MethodTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Query\Response\QueryResponseInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\DefaultFileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Entity\DomainEntityInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Infrastructure\Factory\DefaultFactory\FactoryCaseInterface;

class DomainToQueryResponseFactoryInterfaceCase extends DefaultFileCase implements ArchitectureFileCaseInterface, FactoryCaseInterface
{
    public function __construct(
        private readonly AttributesCreatorFacade $attributesCreatorFacade,
        private readonly QueryResponseInterfaceCase $queryResponseInterfaceCase,
        private readonly DomainEntityInterfaceCase $domainEntityInterfaceCase
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        $domainEntityInterfaceUse = $this->domainEntityInterfaceCase->createUse($caseParameters);
        $queryResponseInterfaceUse = $this->queryResponseInterfaceCase->createUse($caseParameters);
        $collectionInterfaceUse = $this->attributesCreatorFacade->createUse(CollectionInterface::class);
        $mappingMethod = $this->attributesCreatorFacade->createMethod(
            'mapping',
            $queryResponseInterfaceUse->getClassName(),
            $this->createCollection()->add(
                $this->attributesCreatorFacade->createParameterByUse(
                    'entity',
                    $this->domainEntityInterfaceCase->createUse($caseParameters),
                    false,
                    null,
                    null,
                    false
                )
            ),
            MethodTypeEnum::NON_STATIC,
            ModificationTypeEnum::PUBLIC_,
            true
        );
        $mappingCollectionMethod = $this->attributesCreatorFacade->createMethod(
            'mappingCollection',
            $collectionInterfaceUse->getClassName(),
            $this->createCollection()->add(
                $this->attributesCreatorFacade->createParameterByUse(
                    'domainEntityCollection',
                    $collectionInterfaceUse,
                    false,
                    null,
                    null,
                    false
                )
            ),
            MethodTypeEnum::NON_STATIC,
            ModificationTypeEnum::PUBLIC_,
            true
        );

        return
            $this->createDefault($caseParameters)
                ->setClassType(ClassTypeEnum::INTERFACE_)
                ->setMethodCollection(
                    $this->createCollection()
                        ->add($mappingMethod)
                        ->add($mappingCollectionMethod)
                )->setUseNamespaceCollection(
                    $this->createCollection()
                        ->add($queryResponseInterfaceUse)
                        ->add($collectionInterfaceUse)
                        ->add($domainEntityInterfaceUse)
                );
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto
    {
        return $this->createDefaultUse($this->attributesCreatorFacade, $fileCase, $isArray);
    }
}
