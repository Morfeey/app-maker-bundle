<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Facade;

use App\Bundles\InfrastructureBundle\Application\Contract\Facade\ContractFacadeInterface;
use App\Bundles\InfrastructureBundle\Application\Contract\Facade\DefaultFacade\DefaultFacadeByFilterInterface;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Facade\NonArchitect\Service\FacadeService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Filter\Entity\FieldListInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Filter\FilterInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Query\NonArchitect\Context\HandlerInterfaceContext;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\DefaultFileCase;
use ReflectionClass;

class FacadeInterfaceCase extends DefaultFileCase implements ArchitectureFileCaseInterface
{

    public function __construct(
        protected readonly AttributesCreatorFacade $attributesCreatorFacade,
        protected readonly FacadeService $facadeService,
        protected readonly HandlerInterfaceContext $handlerInterfaceContext,
        protected readonly FilterInterfaceCase $filterInterfaceCase,
        protected readonly FieldListInterfaceCase $fieldListInterfaceCase
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        $docCollection = $this->createCollection();
        $handlerUses = $this->createCollection();
        foreach ($this->handlerInterfaceContext->create() as $handler) {
            $handlerUses->mergeWithoutReplacement($handler->create($caseParameters)->getUseNamespaceCollection());
            $method = $this->facadeService->createMethodByHandler($handler, $caseParameters, true);

            $docCollection->add(
                $this->facadeService->createDocByMethod($method, ...$this->facadeService->createReturnsByMethod($method, $caseParameters))
            );
        }

        return
            $this->createDefault($caseParameters)
                ->setClassType(ClassTypeEnum::INTERFACE_)
                ->setUseNamespaceCollection($handlerUses)
                ->setDocCollection($docCollection->mergeWithoutReplacement($this->createContractDocCollection($caseParameters)))
                ->setDescription($this->attributesCreatorFacade->createDocDescription('Contract facade for use any namespaces'))
                ->setExtendsCollection(
                    $this->createCollection()
                        ->add($this->attributesCreatorFacade->createUse(ContractFacadeInterface::class))
                        ->add($this->attributesCreatorFacade->createUse(DefaultFacadeByFilterInterface::class))
                );
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto
    {
        return $this->createDefaultUse($this->attributesCreatorFacade, $fileCase, $isArray);
    }

    private function createContractDocCollection(FileCase $caseParameters): CollectionInterface
    {
        $docCollection = $this->createCollection();
        $filterUse = $this->filterInterfaceCase->createUse($caseParameters);
        $fieldList = $this->fieldListInterfaceCase->createUse($caseParameters);
        $typehints = [
            'createFilter' => $filterUse,
            'createFieldList' => $fieldList
        ];
        $contractFacadeReflection = new ReflectionClass(ContractFacadeInterface::class);
        foreach ($contractFacadeReflection->getMethods() as $method) {
            /** @var UseNamespaceDto $typehint */
            $typehint = $typehints[$method->getName()] ?? null;
            $contractMethod = $this->attributesCreatorFacade->createMethod(
                $method->getName(),
                $typehint?->getClassName(),
                $this->createCollection()
            );

            $doc = $this->facadeService->createDocByMethod($contractMethod);
            if ($typehint) {
                $doc = $this->facadeService->createDocByMethod($contractMethod, $typehint);
            }

            $docCollection->add($doc);
        }

        return $docCollection;
    }
}
