<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Infrastructure\Factory\DefaultFactory;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Infrastructure\Factory\DefaultFactory\FactoryCaseInterface;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\Collection;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Service\GetterService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Service\SetterService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\MethodTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Command\Dto\CommandQueryDtoCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Factory\CommandQueryDtoToEntityFactoryInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\DefaultFileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Entity\DomainEntityInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\RepositoryInterfaceCase;

class CommandQueryToDomainEntityFactoryCase extends DefaultFileCase implements FactoryCaseInterface
{
    public function __construct(
        private readonly RepositoryInterfaceCase $repositoryInterfaceCase,
        private readonly AttributesCreatorFacade $attributesCreatorFacade,
        private readonly DomainEntityInterfaceCase $domainEntityInterfaceCase,
        private readonly SetterService $setterService,
        private readonly GetterService $getterService,
        private readonly CommandQueryDtoCase $commandQueryDtoCase,
        private readonly CommandQueryDtoToEntityFactoryInterfaceCase $commandQueryDtoToEntityFactoryInterfaceCase
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        $commandQueryDtoToEntityFactoryInterfaceUse = $this->commandQueryDtoToEntityFactoryInterfaceCase->createUse($caseParameters);
        $repositoryUse = $this->repositoryInterfaceCase->createUse($caseParameters);
        $repositoryParameter = $this->attributesCreatorFacade->createParameterByUse('repository', $repositoryUse);
        $constructor = $this->attributesCreatorFacade->createConstructor(null, $repositoryParameter);

        $entityInterfaceUse = $this->domainEntityInterfaceCase->createUse($caseParameters);
        $commandQueryDtoUse = $this->commandQueryDtoCase->createUse($caseParameters);
        $collectionInterfaceUse = $this->attributesCreatorFacade->createUse(CollectionInterface::class);
        $collectionUse = $this->attributesCreatorFacade->createUse(Collection::class);

        $methodMapping = $this->attributesCreatorFacade->createMethod(
            'mapping',
            $entityInterfaceUse->getClassName(),
            $this->createCollection()->add(
                $this->attributesCreatorFacade->createParameterByUse(
                    'commandQueryDto',
                    $commandQueryDtoUse,
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
        $methodMappingCollection = $this->attributesCreatorFacade->createMethod(
            'mappingCollection',
            $collectionInterfaceUse->getClassName(),
            $this->createCollection()->add(
                $this->attributesCreatorFacade->createParameterByUse(
                    'commandQueryDtoCollection',
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
                ->setConstructor($constructor)
                ->setMethodCollection(
                    $this->createCollection()->add($methodMapping)->add($methodMappingCollection)
                )
                ->setImplementsCollection(
                    $this->createCollection()->add($commandQueryDtoToEntityFactoryInterfaceUse)
                )
                ->setUseNamespaceCollection(
                    $this->createCollection()
                        ->add($repositoryUse)
                        ->add($entityInterfaceUse)
                        ->add($commandQueryDtoUse)
                        ->add($collectionInterfaceUse)
                        ->add($collectionUse)
                        ->add($commandQueryDtoToEntityFactoryInterfaceUse)
                );
    }

    private function createMappingCollectionContent(FileCase $fileCaseDto): string
    {
        $commandQueryDtoUse = $this->commandQueryDtoCase->createUse($fileCaseDto);
        $contentLineList = [
            'return Collection::createFrom(',
            '           array_map(',
            "               function ({$commandQueryDtoUse->getClassName()} \$item) {",
            "                   return \$this->mapping(\$item);",
            '               },',
            "               \$commandQueryDtoCollection->toArray()",
            '           )',
            '       );'
        ];

        return implode(PHP_EOL, $contentLineList);
    }

    private function createMappingContent(FileCase $fileCaseDto): string
    {
        $contentList = [
            '$entity = $this->repository->create()'
        ];
        $propId = null;
        foreach ($fileCaseDto->getReflectionEntity()->getProperties() as $property) {
            if ($property->getName() === 'id') {
                $propId = $property;
                continue;
            }

            $propertySetterName = $this->setterService->createNameByProperty($property);
            $propertyGetterName = $this->getterService->createNameByProperty($property);

            $contentList[] = "           ->{$propertySetterName}(\$commandQueryDto->{$propertyGetterName}())";
        }

        $contentList[] = '        ;';
        if ($propId) {
            $contentList[] = "         if (\$commandQueryDto->{$this->getterService->createNameByProperty($propId)}()) {";
            $contentList[] = "              \$entity->{$this->setterService->createNameByProperty($propId)}(\$commandQueryDto->{$this->getterService->createNameByProperty($propId)}());";
            $contentList[] = "         }";
        }

        $contentList[] = '';
        $contentList[] = '         return $entity;';

        return implode(PHP_EOL, $contentList);
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto
    {
        return $this->createDefaultUse($this->attributesCreatorFacade, $fileCase, $isArray);
    }
}
