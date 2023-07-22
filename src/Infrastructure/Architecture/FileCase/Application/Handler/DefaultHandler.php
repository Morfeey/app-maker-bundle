<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Handler;

use App\Bundles\InfrastructureBundle\Application\Contract\Handler\CommandHandlerInterface;
use App\Bundles\InfrastructureBundle\Application\Contract\Handler\QueryHandlerInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ConstructorDto as Constructor;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\MethodDto as Method;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ParameterDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\MethodTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\HandlerInterfaceCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\NonArchitect\Enum\RequestCqrsType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\DefaultFileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\RepositoryInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Infrastructure\Factory\DefaultFactory\FactoryCaseInterface;

abstract class DefaultHandler extends DefaultFileCase
{
    public const REPOSITORY_DEPENDENCY_NAME = 'repository';
    public const FACTORY_DEPENDENCY_NAME = 'factory';
    public const FACTORY_METHOD_NAME = 'mappingCollection';

    public function createInvoke(
        HandlerInterfaceCaseInterface $case,
        FileCase $caseParameters,
        AttributesCreatorFacade $attributesCreatorFacade,
        bool $issetFactoryDependency = false
    ): ?Method {
        $handlerInterfaceCase = $case->create($caseParameters);
        if (!$handlerInterfaceCase->getMethodCollection()->count()) {
            return null;
        }

        /** @var $invokeInterfaceMethod Method */
        $invokeInterfaceMethod = $handlerInterfaceCase->getMethodCollection()->first();

        return $attributesCreatorFacade->createMethod(
            $invokeInterfaceMethod->getName(),
            $invokeInterfaceMethod->getTypeHint(),
            $invokeInterfaceMethod->getParameters(),
            MethodTypeEnum::NON_STATIC,
            ModificationTypeEnum::PUBLIC_,
            false,
            $this->createContentInvoke($case, $caseParameters, $issetFactoryDependency),
            $invokeInterfaceMethod->getCase(),
            ...$invokeInterfaceMethod->getUseCollection()->toArray()
        );
    }

    public function createConstructor(AttributesCreatorFacade $attributesCreatorFacade, ParameterDto ...$parameters): Constructor
    {
        return $attributesCreatorFacade->createConstructor(
            $this->createConstructorContent(),
            ...$parameters
        );
    }

    public function createDependency(
        string $name,
        ArchitectureFileCaseInterface $case,
        AttributesCreatorFacade $attributesCreatorFacade,
        FileCase $caseParameters
    ): ParameterDto {
        return $attributesCreatorFacade->createParameterByUse($name, $case->createUse($caseParameters));
    }

    public function createConstructorContent(): ?string
    {
        return null;
    }

    protected function createContentInvoke(
        HandlerInterfaceCaseInterface $handlerInterfaceCase,
        FileCase $caseParameters,
        bool $issetFactoryDependency = false
    ): string {
        $request = $handlerInterfaceCase->getRequest();
        $queryNamespace = $request->createUse($caseParameters);
        $repositoryMethodName =
            $this->createPrototypeStringValue()
                ->setString($queryNamespace->getAlias() ?: $queryNamespace->getClassName())
                ->replace($this->getEntityNameWithoutEntity($caseParameters->getServiceDto()->getNamespace()))
                ->toCamelCase()
                ->firstCharLow()
                ->replace('Query')
                ->replace('Command')
                ->getResult();

        $repositoryParameters = [];
        $queryCase = $request->create($caseParameters);
        foreach ($queryCase->getConstructor()->getDependencies() as $dependency) {
            $lineSeparator = '                ';
            if ($issetFactoryDependency) {
                $lineSeparator .= '    ';
            }

            /** @var Method $getter */
            $getter = $request->createGettersByParameters($dependency)->first();
            $variadicPart = $dependency->getType()->isVariadic() ? '...' : '';
            $toArrayPart = '';
            if ($dependency->getUseCollection() !== null && $dependency->getUseCollection()->count()) {
                foreach ($dependency->getUseCollection() as $use) {
                    if ($use->getClassName() === 'CollectionInterface' || $use->getClassName() === 'Collection') {
                        $toArrayPart = '->toArray()';
                        break;
                    }
                }

                if ($toArrayPart === '') {
                    $toArrayPart = $getter->getTypeHint() === 'CollectionInterface' || $getter->getTypeHint() === 'Collection' ? '->toArray()' : '';
                }
            }

            $repositoryParameters[] = $lineSeparator . $variadicPart . '$' . $request->getType()->getValue() . '->' . $getter->getName() . '()' . $toArrayPart;
        }

        /** @var $invokeInterfaceMethod Method */
        $invokeInterfaceMethod = $handlerInterfaceCase->create($caseParameters)->getMethodCollection()->first();
        $invokeTypeHint = $invokeInterfaceMethod->getTypeHint();
        $isNullable = stripos($invokeTypeHint, '?') !== false;

        $factoryMethodName = static::FACTORY_METHOD_NAME;
        $repositoryParameters = implode(',' . PHP_EOL, $repositoryParameters);
        $contentLineList = [
            $isNullable ? '$response = ' : 'return',
            "           \$this->repository->{$repositoryMethodName}(",
            $repositoryParameters,
            '           );',
        ];

        if ($issetFactoryDependency) {
            $contentLineList = [
                'return',
                "           \$this->factory->{$factoryMethodName}(",
                "               \$this->repository->{$repositoryMethodName}(",
                $repositoryParameters,
                '               )',
                '           );'
            ];
        }

        if ($isNullable) {
            $contentLineList = [
                "\$response = \$this->repository->{$repositoryMethodName}(",
                $repositoryParameters,
                '       );',
                '       if (null === $response) {',
                '           return null;',
                '       }',
                '',
                $issetFactoryDependency ? "         return \$this->factory->{$factoryMethodName}(\$response);" : '           return  $response;'
            ];
        }

        return implode(PHP_EOL, $contentLineList);
    }

    public function createDefaultCase(
        FileCase $architectureFileCaseDto,
        HandlerInterfaceCaseInterface $handlerInterfaceCase,
        AttributesCreatorFacade $attributesCreatorFacade,
        ?RepositoryInterfaceCase $repositoryInterfaceCase = null,
        ?FactoryCaseInterface $factoryCase = null
    ): ArchitectureFileDto {
        $constructor = null;
        $dependencies = [];
        if ($repositoryInterfaceCase) {
            $dependencies[] =
                $this->createDependency(
                    static::REPOSITORY_DEPENDENCY_NAME,
                    $repositoryInterfaceCase,
                    $attributesCreatorFacade,
                    $architectureFileCaseDto
            );
        }

        $isUseFactoryDependency = false;
        if ($factoryCase) {
            $isUseFactoryDependency = true;
            $dependencies[] =
                $this->createDependency(
                    static::FACTORY_DEPENDENCY_NAME,
                    $factoryCase,
                    $attributesCreatorFacade,
                    $architectureFileCaseDto
            );
        }

        if ($dependencies) {
            $constructor = $this->createConstructor(
                $attributesCreatorFacade,
                ...$dependencies
            );
        }

        $methodCollection = $this->createCollection();
        $invoke = $this->createInvoke($handlerInterfaceCase, $architectureFileCaseDto, $attributesCreatorFacade, $isUseFactoryDependency);
        if ($invoke) {
            $methodCollection->add($invoke);
        }

        $defaultInterfaceUse = $attributesCreatorFacade->createUse(QueryHandlerInterface::class);
        if ($handlerInterfaceCase->getRequest()->getType() === RequestCqrsType::COMMAND) {
            $defaultInterfaceUse = $attributesCreatorFacade->createUse(CommandHandlerInterface::class);
        }

        return
            $this->createDefault($architectureFileCaseDto)
                ->setClassType(ClassTypeEnum::CLASS_)
                ->setConstructor($constructor)
                ->setMethodCollection($methodCollection)
                ->setUseNamespaceCollection($this->createCollection()->mergeWithoutReplacement($handlerInterfaceCase->create($architectureFileCaseDto)->getUseNamespaceCollection()))
                ->setImplementsCollection(
                    $this->createCollection()
                        ->add($defaultInterfaceUse)
                        ->add($handlerInterfaceCase->createUse($architectureFileCaseDto))
                );
    }
}
