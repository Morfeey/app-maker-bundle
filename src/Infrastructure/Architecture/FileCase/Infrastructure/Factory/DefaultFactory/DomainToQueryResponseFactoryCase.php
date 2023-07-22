<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Infrastructure\Factory\DefaultFactory;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Infrastructure\Factory\DefaultFactory\FactoryCaseInterface;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\Collection;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\MethodTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Query\Response\QueryResponseInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Factory\DomainToQueryResponseFactoryInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Handler\Query\Response\QueryResponseCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\DefaultFileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Entity\DomainEntityInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Entity\Getters\DomainEntityGettersInterfaceCase;

class DomainToQueryResponseFactoryCase extends DefaultFileCase implements FactoryCaseInterface
{
    public function __construct(
        private readonly AttributesCreatorFacade $attributesCreatorFacade,
        private readonly QueryResponseInterfaceCase $queryResponseInterfaceCase,
        private readonly QueryResponseCase $queryResponseCase,
        private readonly DomainEntityInterfaceCase $domainEntityInterfaceCase,
        private readonly DomainEntityGettersInterfaceCase $domainEntityGettersInterfaceCase,
        private readonly DomainToQueryResponseFactoryInterfaceCase $domainToQueryResponseFactoryInterfaceCase
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        $domainToQueryResponseFactoryInterfaceCase = $this->domainToQueryResponseFactoryInterfaceCase->createUse($caseParameters);
        $domainEntityInterfaceUse = $this->domainEntityInterfaceCase->createUse($caseParameters);
        $queryResponseInterfaceUse = $this->queryResponseInterfaceCase->createUse($caseParameters);
        $queryResponseUse = $this->queryResponseCase->createUse($caseParameters);
        $collectionInterfaceUse = $this->attributesCreatorFacade->createUse(CollectionInterface::class);
        $collectionUse = $this->attributesCreatorFacade->createUse(Collection::class);
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
            false,
            $this->createMappingContent($caseParameters)
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
            false,
            $this->createMappingCollectionContent($caseParameters)
        );

        return
            $this->createDefault($caseParameters)
                ->setClassType(ClassTypeEnum::CLASS_)
                ->setMethodCollection(
                    $this->createCollection()
                        ->add($mappingMethod)
                        ->add($mappingCollectionMethod)
                )->setUseNamespaceCollection(
                    $this->createCollection()
                        ->add($queryResponseInterfaceUse)
                        ->add($queryResponseUse)
                        ->add($collectionInterfaceUse)
                        ->add($collectionUse)
                        ->add($domainEntityInterfaceUse)
                )->setImplementsCollection(
                    $this->createCollection()->add($domainToQueryResponseFactoryInterfaceCase)
                );
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto
    {
        return $this->createDefaultUse($this->attributesCreatorFacade, $fileCase, $isArray);
    }

    private function createMappingCollectionContent(FileCase $fileCase): string
    {
        $domainEntityInterfaceUse = $this->domainEntityInterfaceCase->createUse($fileCase);
        $contentLineList = [
            'return Collection::createFrom(',
            '           array_map(',
            "               function ({$domainEntityInterfaceUse->getClassName()} \$item) {",
            "                   return \$this->mapping(\$item);",
            '               },',
            "               \$domainEntityCollection->toArray()",
            '           )',
            '       );'
        ];

        return implode(PHP_EOL, $contentLineList);
    }

    private function createMappingContent(FileCase $fileCase): string
    {
        $queryResponseUse = $this->queryResponseCase->createUse($fileCase);
        $contentLineList = [
            "return new {$queryResponseUse->getClassName()}("
        ];
        $getters = $this->domainEntityGettersInterfaceCase->create($fileCase)->getMethodCollection();
        foreach ($getters as $index => $getter) {
            $separator = !$getters->isLast($index) ? ',' : '';
            $contentLineList[] = "          \$entity->{$getter->getName()}()" . $separator;
        }

        $contentLineList[] = '        );';

        return implode(PHP_EOL, $contentLineList);
    }
}
