<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Facade;

use App\Bundles\InfrastructureBundle\Application\Contract\Bus\CommandBusInterface;
use App\Bundles\InfrastructureBundle\Application\Contract\Bus\QueryBusInterface;
use App\Bundles\InfrastructureBundle\Application\Contract\Facade\ContractFacadeInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Enum\MethodCaseEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\DocTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\MethodTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Facade\FacadeInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Facade\NonArchitect\Service\FacadeService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Filter\Entity\FieldListInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Filter\FilterInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Query\NonArchitect\Context\HandlerInterfaceContext;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\DefaultFileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\ConstructorCreatorService;
use ReflectionClass;

class FacadeCase extends DefaultFileCase implements ArchitectureFileCaseInterface
{
    public function __construct(
        private readonly AttributesCreatorFacade $attributesCreatorFacade,
        private readonly HandlerInterfaceContext $handlerInterfaceContext,
        private readonly FacadeInterfaceCase $facadeInterfaceCase,
        private readonly FieldListInterfaceCase $fieldListInterfaceCase,
        private readonly FilterInterfaceCase $filterInterfaceCase,
        private readonly FacadeService $facadeService
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        $methods = $this->createCollection();
        $uses = $this->createCollection();
        foreach ($this->handlerInterfaceContext->create() as $handlerInterface) {
            $uses->mergeWithoutReplacement($handlerInterface->create($caseParameters)->getUseNamespaceCollection());
            $uses->add($handlerInterface->getRequest()->createUse($caseParameters));
            $method = $this->facadeService->createMethodByHandler($handlerInterface, $caseParameters, false, true);
            $methods->add($method);
        }

        $dependencies = $this->createCollection();
        $contractFacadeInterfaceReflection = new ReflectionClass(ContractFacadeInterface::class);
        foreach ($contractFacadeInterfaceReflection->getMethods() as $method) {
            $fieldName = $this->createPrototypeStringValue()
                ->setString($method->getName())
                ->replace('create')
                ->toCamelCase()
                ->firstCharLow();

            $docCollection = $this->createCollection();
            $typeHint = $this->attributesCreatorFacade->createUse($method->getReturnType()->getName());
            $contractMethod = $this->attributesCreatorFacade->createMethod(
                $method->getName(),
                $typeHint->getClassName(),
                $this->createCollection(),
                MethodTypeEnum::NON_STATIC,
                ModificationTypeEnum::PUBLIC_,
                false,
                "return clone \$this->{$fieldName->getResult()};",
                MethodCaseEnum::GETTER,
                $typeHint
            );


            if ($fieldName->startsWith('filter')) {
                $filterUse = $this->filterInterfaceCase->createUse($caseParameters);
                $dependency = $this->attributesCreatorFacade->createParameter(
                    $fieldName->getResult(),
                    $this->attributesCreatorFacade->createType(
                        $filterUse->getClassName(),
                        $filterUse,
                        false,
                        true
                    ),
                    null,
                    ModificationTypeEnum::PRIVATE_,
                    true
                );

                $docCollection->add(
                    $this->attributesCreatorFacade->createDoc(
                        DocTypeEnum::RETURN,
                        $contractMethod,
                        $filterUse
                    )
                );
                $dependencies->add($dependency);
            }

            if ($fieldName->isContains('field')) {
                $fieldListUse = $this->fieldListInterfaceCase->createUse($caseParameters);
                $dependency = $this->attributesCreatorFacade->createParameter(
                    $fieldName->getResult(),
                    $this->attributesCreatorFacade->createType(
                        $fieldListUse->getClassName(),
                        $fieldListUse,
                        false,
                        true
                    ),
                    null,
                    ModificationTypeEnum::PRIVATE_,
                    true
                );

                $docCollection->add(
                    $this->attributesCreatorFacade->createDoc(
                        DocTypeEnum::RETURN,
                        $contractMethod,
                        $fieldListUse
                    )
                );
                $dependencies->add($dependency);
            }

            $methods->add($contractMethod->setDocCollection($docCollection));
        }

        $queryBusUse = $this->attributesCreatorFacade->createUse(QueryBusInterface::class);
        $commandBusUse = $this->attributesCreatorFacade->createUse(CommandBusInterface::class);
        $dependencies->add(
            $this->attributesCreatorFacade->createParameter(
                $this->createPrototypeStringValue()
                    ->setString($queryBusUse->getClassName())
                    ->replace('Interface')
                    ->toCamelCase()
                    ->firstCharLow()
                    ->getResult(),
                $this->attributesCreatorFacade->createType($queryBusUse->getClassName(), $queryBusUse),
                null,
                ModificationTypeEnum::PRIVATE_,
                true,
                $queryBusUse
            )
        )->add(
            $this->attributesCreatorFacade->createParameter(
                $this->createPrototypeStringValue()
                    ->setString($commandBusUse->getClassName())
                    ->replace('Interface')
                    ->toCamelCase()
                    ->firstCharLow()
                    ->getResult(),
                $this->attributesCreatorFacade->createType($commandBusUse->getClassName(), $commandBusUse),
                null,
                ModificationTypeEnum::PRIVATE_,
                true,
                $commandBusUse
            )
        );

        return
            $this->createDefault($caseParameters)
                ->setClassType(ClassTypeEnum::CLASS_)
                ->setMethodCollection($methods)
                ->setUseNamespaceCollection($uses)
                ->setConstructor(
                    $this->attributesCreatorFacade->createConstructor(null, ...$dependencies->toArray())
                )
                ->setImplementsCollection(
                    $this->createCollection()
                        ->add($this->facadeInterfaceCase->createUse($caseParameters))
                );
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto
    {
        return $this->createDefaultUse($this->attributesCreatorFacade, $fileCase, $isArray);
    }
}
