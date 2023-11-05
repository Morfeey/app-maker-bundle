<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Filter;

use App\Bundles\InfrastructureBundle\Application\Filter\DefaultContractFilter;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Enum\MethodCaseEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\MethodTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Filter\Entity\FieldListInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Filter\FilterInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\RepositoryInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\CreatorDefaultArchitectureFileDtoHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\ConstructorCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\TypeCreatorService;
use ReflectionProperty;

class ContractFilterCase implements ArchitectureFileCaseInterface
{

    use CreatorDefaultArchitectureFileDtoHelper, PrototypeHelper;
    public function __construct(
        private readonly FilterInterfaceCase $filterInterfaceCase,
        private readonly RepositoryInterfaceCase $repositoryInterfaceCase,
        private readonly FieldListInterfaceCase $fieldListInterfaceCase,
        private readonly AttributesCreatorFacade $attributesCreatorFacade,
        private readonly ConstructorCreatorService $constructorCreatorService,
        private readonly TypeCreatorService $typeCreatorService
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        $byMethods = $this->createCollection();
        $byListMethods = $this->createCollection();
        foreach ($caseParameters->getReflectionEntity()->getProperties() as $property) {
            $byMethods->add($this->filterInterfaceCase->createByMethod($property, $this->createByMethodContent($property, $property->getName())));
            if ($property->getType()?->getName() === 'bool') {
                continue;
            }

            $byListMethods->add($this->filterInterfaceCase->createByListMethod($property, $this->createByMethodContent($property, $property->getName() . 's')));
        }

        $repositoryUse = $this->repositoryInterfaceCase->createUse($caseParameters);
        $repositoryMethod = $this->attributesCreatorFacade->createMethod(
            'createRepository',
            $repositoryUse->getClassName(),
            $this->createCollection(),
            MethodTypeEnum::NON_STATIC,
            ModificationTypeEnum::PUBLIC_,
            false,
            $this->createRepositoryContent(),
            MethodCaseEnum::SETTER,
            $repositoryUse
        );

        $fieldListUse = $this->fieldListInterfaceCase->createUse($caseParameters);
        $fieldListMethod = $this->attributesCreatorFacade->createMethod(
            'createFieldList',
            $fieldListUse->getClassName(),
            $this->createCollection(),
            MethodTypeEnum::NON_STATIC,
            ModificationTypeEnum::PUBLIC_,
            false,
            $this->createFieldListContent(),
            MethodCaseEnum::SETTER,
            $fieldListUse
        );

        $constructor = $this->constructorCreatorService->create(
            $this->createConstructorContent(),
            $this->attributesCreatorFacade->createParameter(
                'repository',
                $this->typeCreatorService->create(false, true, $repositoryUse->getClassName()),
                null,
                ModificationTypeEnum::PRIVATE_,
                true,
                $repositoryUse
            ),
            $this->attributesCreatorFacade->createParameter(
                'fieldList',
                $this->typeCreatorService->create(false, true, $fieldListUse->getClassName()),
                null,
                ModificationTypeEnum::PRIVATE_,
                true,
                $fieldListUse
            )
        );

        return
            $this->createDefault($caseParameters)
                ->setConstructor($constructor)
                ->setMethodCollection(
                    $this->createCollection()
                        ->mergeWithoutReplacement($byMethods, $byListMethods)
                        ->add($repositoryMethod)
                        ->add($fieldListMethod)
                )
                ->setExtendsCollection($this->createCollection()->add($this->attributesCreatorFacade->createUse(DefaultContractFilter::class)))
                ->setImplementsCollection($this->createCollection()->add($this->filterInterfaceCase->createUse($caseParameters)));
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto
    {
        $fileCase = $this->attributesCreatorFacade->createCaseDtoByCase($fileCase, $this);

        return $this->attributesCreatorFacade->createUse(
            $fileCase->getCaseDto()->getNamespace(),
            null,
            null,
            $isArray
        );
    }

    protected function createByMethodContent(ReflectionProperty $property, string $parameterName): string
    {
        return
        "return \$this->addCondition(
            \$this->createCondition(\${$parameterName}, \$this->fieldList->{$property->getName()}(), \$type, \$where)
        );";
    }

    protected function createRepositoryContent(): string
    {
        return 'return $this->repository;';
    }

    protected function createFieldListContent(): string
    {
        return 'return clone $this->fieldList;';
    }

    protected function createConstructorContent(): string
    {
        return 'parent::__construct();';
    }
}
