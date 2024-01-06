<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service;

use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\Collection;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Context\MethodCreatorContext;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Dto\MethodParametersDto as MethodParameters;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Enum\MethodCaseEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Service\GetterService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Service\SetterService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Creator\ArchitectureCreator;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ConstructorDto as Constructor;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DefaultValue;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocDescriptionDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocTypeInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\FieldDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\MethodDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ParameterDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\TypeDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum as ClassType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\DocTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\MethodTypeEnum as MethodType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\MultiTypeHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\NamespaceHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\ConstructorCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\DefaultValueCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\DocCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\FieldCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\MethodCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\ParameterCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\TypeCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\UseCreatorService;

class AttributesCreatorFacade
{
    use NamespaceHelper, PrototypeHelper, MultiTypeHelper;
    public function __construct(
        private readonly MethodCreatorContext $methodCreatorContext,
        private readonly FileUseCollectorService $useCollectorService,
        private readonly UseCreatorService $useCreatorService,
        private readonly DocCreatorService $docCreatorService,
        private readonly ArchitectureCreator $architectureCreator,
        private readonly MethodCreatorService $methodCreatorService,
        private readonly ParameterCreatorService $parameterCreatorService,
        private readonly ConstructorCreatorService $constructorCreatorService,
        private readonly TypeCreatorService $typeCreatorService,
        private readonly DefaultValueCreatorService $defaultValueCreatorService,
        private readonly FieldCreatorService $fieldCreatorService,
        private readonly GetterService $getterService,
        private readonly SetterService $setterService
    ) {
    }

    /**
     * @return CollectionInterface<MethodDto>|MethodDto[]
     */
    public function createMethods(MethodParameters $parameters): CollectionInterface
    {
        return $this->methodCreatorContext->create($parameters)?->create($parameters) ?? new Collection();
    }

    public function createMethodParameters(
        ArchitectureFileCaseDto $architectureFileCas,
        MethodCaseEnum          $case,
        ClassType               $classType = ClassType::CLASS_,
        ModificationTypeEnum    $modificationType = ModificationTypeEnum::PUBLIC_,
        MethodType              $type = MethodType::NON_STATIC,
        bool                    $isVirtual = false
    ): MethodParameters {
        return new MethodParameters($architectureFileCas, $case, $classType, $modificationType, $type, $isVirtual);
    }

    public function createMethod(
        string               $name,
        string               $typeHint,
        CollectionInterface  $parameters,
        MethodType           $type = MethodType::NON_STATIC,
        ModificationTypeEnum $modificationType = ModificationTypeEnum::PUBLIC_,
        bool                 $isVirtual = false,
        ?string              $content = null,
        MethodCaseEnum       $case = MethodCaseEnum::GETTER,
        ?UseNamespaceDto ...$usesNamespace
    ): MethodDto {
        return $this->methodCreatorService->create($parameters, $typeHint, $name, $type, $modificationType, $isVirtual, $content, $case, ...$usesNamespace);
    }

    public function collectUse(ArchitectureFileDto $architectureFile): CollectionInterface
    {
        return $this->useCollectorService->collect($architectureFile);
    }

    public function createUse(string $namespace, ?string $class = null, ?string $alias = null, bool $isArray = false): UseNamespaceDto
    {
        if (!$class) {
            $class = $this->getClassNameByNamespace($namespace);
        }

        return $this->useCreatorService->create($namespace, $class, $alias, $isArray);
    }

    public function createDoc(DocTypeEnum $type, DocTypeInterface $entity, UseNamespaceDto ...$useNamespaceDtos): DocDto
    {
        return $this->docCreatorService->create($type, $entity, ...$useNamespaceDtos);
    }

    public function createDocDescription(string $description): DocDescriptionDto
    {
        return $this->docCreatorService->createDescription($description);
    }

    public function createCaseDtoByCase(ArchitectureFileCaseDto $randomCase, ArchitectureFileCaseInterface $case): ArchitectureFileCaseDto
    {
        return $this->architectureCreator->createFileCase($randomCase->getEntity()::class, $case);
    }

    public function createParameter(
        string                $name,
        TypeDto               $type,
        ?DefaultValue         $defaultValue = null,
        ?ModificationTypeEnum $modificationType = null,
        bool                  $isReadonly = false,
        UseNamespaceDto ...$usesNamespaceDto
    ): ParameterDto {
        return $this->parameterCreatorService->create($name, $type, $defaultValue, $modificationType, $isReadonly, ...$usesNamespaceDto);
    }

    public function createConstructor(?string $content = null, ParameterDto ...$dependencies): Constructor
    {
        return $this->constructorCreatorService->create($content, ...$dependencies);
    }

    public function createParameterByUse(
        string $name,
        UseNamespaceDto $useNamespaceDto,
        bool $isVariadic = false,
        ?ModificationTypeEnum $modificationType = ModificationTypeEnum::PRIVATE_,
        ?string $defaultValue = null,
        bool $isReadOnly = true
    ): ParameterDto {
        return $this->createParameter(
            $name,
            $this->createType(
                $useNamespaceDto->getAlias() ?: $useNamespaceDto->getClassName(),
                $useNamespaceDto,
                $isVariadic,
                true
            ),
            $defaultValue ? $this->defaultValueCreatorService->create(null, $defaultValue) : null,
            $modificationType,
            $isReadOnly,
            $useNamespaceDto
        );
    }

    public function createFieldByUse(
        string                $name,
        UseNamespaceDto       $useNamespaceDto,
        bool                  $isVariadic = false,
        ?ModificationTypeEnum $modificationType = ModificationTypeEnum::PRIVATE_,
        ?string               $defaultValue = null,
        bool                  $isReadOnly = false
    ): FieldDto {
        return $this->createField(
            $name,
            $this->createType(
                $useNamespaceDto->getAlias() ?: $useNamespaceDto->getClassName(),
                $useNamespaceDto,
                $isVariadic,
                true
            ),
            $defaultValue ? $this->defaultValueCreatorService->create(null, $defaultValue) : null,
            $modificationType,
            $isReadOnly,
            $useNamespaceDto
        );
    }

    public function createGetterByParameter(ParameterDto $parameter, bool $isVirtual = false): MethodDto
    {
        $stringType = $parameter->getType()->getStringType();
        $allowsNull = false;
        if ($stringType) {
            $allowsNull = $this->createPrototypeStringValue()->setString($stringType)->isContains('?');
        }

        $useCollection = $this->createCollection()->mergeWithoutReplacement($parameter->getUseCollection());
        $multiType = $this->createMultiType($parameter->getType());
        $useCollection->mergeWithoutReplacement($this->createUseByMultiType($multiType));

        return $this->createMethod(
            $this->getterService->createName($parameter->getName(), $allowsNull),
            $this->createMultiType($parameter->getType())->getStringType(),
            $this->createCollection(),
            MethodType::NON_STATIC,
            ModificationTypeEnum::PUBLIC_,
            $isVirtual,
            $isVirtual ? null : $this->getterService->createContentByPropertyName($parameter->getName()),
            MethodCaseEnum::GETTER,
            ...$useCollection->toArray()
        );
    }

    public function createType(?string $type = null, ?UseNamespaceDto $useNamespaceDto = null, bool $isVariadic = false, bool $isObject = false): TypeDto
    {
        return $this->typeCreatorService->create($isVariadic, $isObject, $type, $useNamespaceDto);
    }

    public function createField(
        string                $name,
        TypeDto               $type,
        ?DefaultValue         $defaultValue = null,
        ?ModificationTypeEnum $modificationType = ModificationTypeEnum::PRIVATE_,
        bool                  $isReadonly = false,
        UseNamespaceDto ...$usesNamespaceDto
    ): FieldDto {
        $type = $this->createMultiType($type);
        $multiTypeUse = $this->createUseByMultiType($type);
        foreach ($usesNamespaceDto as $use) {
            $multiTypeUse->add($use);
        }

        return $this->fieldCreatorService->create($name, $type, $defaultValue, $modificationType, $isReadonly, ...$multiTypeUse->toArray());
    }
}
