<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Query\DefaultQuery;

use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\ArchitectureCqrsRequestInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\DefaultHandlerInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\HandlerInterfaceCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Query\Response\QueryResponseInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\NonArchitect\Enum\RequestCqrsType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Query\DefaultQuery\FindByFilterQueryCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;

class FindByFilterQueryHandlerInterfaceCase extends DefaultHandlerInterfaceCase implements ArchitectureFileCaseInterface, HandlerInterfaceCaseInterface
{
    public function __construct(
        protected readonly AttributesCreatorFacade $attributesCreatorFacade,
        protected readonly FindByFilterQueryCase $findByFilterQueryCase,
        protected QueryResponseInterfaceCase $queryResponseInterfaceCase
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        $collectionInterfaceUse = $this->attributesCreatorFacade->createUse(CollectionInterface::class);
        $queryResponseUse = $this->queryResponseInterfaceCase->createUse($caseParameters);
        $method = $this->createInvokeMethod(
            $this->attributesCreatorFacade,
            $caseParameters,
            $this->findByFilterQueryCase,
            $collectionInterfaceUse->getAlias() ?: $collectionInterfaceUse->getClassName()
        );

        return
            $this->createDefault($caseParameters)
                ->setClassType(ClassTypeEnum::INTERFACE_)
                ->setMethodCollection($this->createCollection()->add($method))
                ->setUseNamespaceCollection(
                    $this->createCollection()
                        ->add($collectionInterfaceUse)
                        ->add($queryResponseUse)
                );
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto
    {
        return $this->createDefaultUse($this->attributesCreatorFacade, $fileCase, $isArray);
    }

    public function getRequest(): ArchitectureCqrsRequestInterface
    {
        return $this->findByFilterQueryCase;
    }

    protected function getType(): RequestCqrsType
    {
        return RequestCqrsType::QUERY;
    }
}
