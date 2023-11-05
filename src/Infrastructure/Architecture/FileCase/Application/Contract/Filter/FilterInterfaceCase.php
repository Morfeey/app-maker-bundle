<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Filter;

use App\Bundles\InfrastructureBundle\Application\Contract\Filter\FilterInterface;
use App\Bundles\InfrastructureBundle\Application\Filter\Condition\Enum\ConditionTypeEnum as Type;
use App\Bundles\InfrastructureBundle\Application\Filter\Condition\Enum\ConditionWhereType as Where;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\MethodDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ParameterDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\MethodTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\CreatorDefaultArchitectureFileDtoHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\NamespaceHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\DefaultValueCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\TypeCreatorService;
use ReflectionProperty;

readonly class FilterInterfaceCase implements ArchitectureFileCaseInterface
{

    use NamespaceHelper, CreatorDefaultArchitectureFileDtoHelper, PrototypeHelper;
    public function __construct(
        private AttributesCreatorFacade $attributesCreatorFacade,
        private TypeCreatorService $typeCreatorService,
        private DefaultValueCreatorService $defaultValueCreatorService
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        $byMethods = $this->createCollection();
        $byListMethods = $this->createCollection();
        foreach ($caseParameters->getReflectionEntity()->getProperties() as $property) {
            $byMethods->add($this->createByMethod($property));
            if ($property->getType()?->getName() === 'bool') {
                continue;
            }

            $byListMethods->add($this->createByListMethod($property));
        }

        return
            $this->createDefault($caseParameters)
                ->setClassType(ClassTypeEnum::INTERFACE_)
                ->setMethodCollection($this->createCollection()->mergeWithoutReplacement($byMethods, $byListMethods))
                ->setExtendsCollection(
                    $this->createCollection()
                        ->add($this->attributesCreatorFacade->createUse(FilterInterface::class))
                );
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

    protected function createWhereParameter(): ParameterDto
    {
        return $this->attributesCreatorFacade->createParameter(
            'where',
            $this->typeCreatorService->create(
                false,
                true,
                'Where',
                $this->attributesCreatorFacade->createUse(Where::class, null, 'Where')
            ),
            $this->defaultValueCreatorService->create(null, 'Where::AND')
        );
    }

    protected function createTypeParameter(bool $isEquals = true): ParameterDto
    {
        $typeUse = $this->attributesCreatorFacade->createUse(Type::class, null, 'Type');
        $typeType = $this->typeCreatorService->create(false, true, 'Type', $typeUse);
        return $this->attributesCreatorFacade->createParameter(
            'type',
            $typeType,
            $this->defaultValueCreatorService->create(null, $isEquals ? 'Type::EQUALS' : 'Type::IN')
        );
    }

    protected function createByParameter(ReflectionProperty $property): ParameterDto
    {
        $byParameterUse = null;
        if ($property->getType()) {
            $byParameterUse = $this->attributesCreatorFacade->createUse($property->getType()->getName());
        }

        return $this->attributesCreatorFacade->createParameter(
            $property->getName(),
            $this->typeCreatorService->create(
                false,
                $byParameterUse !== null,
                '?' . $property->getType()?->getName(),
                $byParameterUse
            ),
            $this->defaultValueCreatorService->create(null, 'null')
        );
    }

    protected function createByListParameter(ReflectionProperty $property): ParameterDto
    {
        $byParameterUse = null;
        if ($property->getType()) {
            $byParameterUse = $this->attributesCreatorFacade->createUse($property->getType()->getName());
        }

        return $this->attributesCreatorFacade->createParameter(
            $property->getName() . 's',
            $this->typeCreatorService->create(false, false, '?array', $byParameterUse),
            $this->defaultValueCreatorService->create(null, 'null')
        );
    }

    public function createByMethod(ReflectionProperty $property, ?string $content = null): MethodDto
    {
        return $this->attributesCreatorFacade->createMethod(
            $this->createByMethodName($property),
            'static',
            $this->createCollection()
                ->add($this->createByParameter($property))
                ->add($this->createTypeParameter())
                ->add($this->createWhereParameter()),
            MethodTypeEnum::NON_STATIC,
            ModificationTypeEnum::PUBLIC_,
            $content === null,
            $content
        );
    }

    public function createByListMethod(ReflectionProperty $property, ?string $content = null): MethodDto
    {
        return $this->attributesCreatorFacade->createMethod(
            $this->createByListMethodName($property),
            'static',
            $this->createCollection()
                ->add($this->createByListParameter($property))
                ->add($this->createTypeParameter(false))
                ->add($this->createWhereParameter()),
            MethodTypeEnum::NON_STATIC,
            ModificationTypeEnum::PUBLIC_,
            $content === null,
            $content
        );
    }

    protected function createByMethodName(ReflectionProperty $property): string
    {
        return 'by' . $this->createPrototypeStringValue()->setString($property->getName())->firstCharUp()->getResult();
    }

    protected function createByListMethodName(ReflectionProperty $property): string
    {
        return $this->createByMethodName($property) . 's';
    }
}
