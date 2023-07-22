<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Query\DefaultQuery;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\ArchitectureCqrsRequestInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\DefaultHandlerInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\HandlerInterfaceCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Query\Response\QueryResponseInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Query\DefaultQuery\FindOneByFilterQueryCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;

class FindOneByFilterQueryHandlerInterfaceCase extends DefaultHandlerInterfaceCase implements ArchitectureFileCaseInterface, HandlerInterfaceCaseInterface
{
    public function __construct(
        private readonly AttributesCreatorFacade $attributesCreatorFacade,
        private readonly FindOneByFilterQueryCase $findOneByFilterQueryCase,
        private readonly QueryResponseInterfaceCase $queryResponseInterfaceCase
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        $responseUse = $this->queryResponseInterfaceCase->createUse($caseParameters);
        $method = $this->createInvokeMethod(
            $this->attributesCreatorFacade,
            $caseParameters,
            $this->findOneByFilterQueryCase,
            '?' . $responseUse->getClassName()
        );

        return
            $this->createDefault($caseParameters)
                ->setClassType(ClassTypeEnum::INTERFACE_)
                ->setMethodCollection($this->createCollection()->add($method))
                ->setUseNamespaceCollection($this->createCollection()->add($responseUse));
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto
    {
        return $this->createDefaultUse($this->attributesCreatorFacade, $fileCase, $isArray);
    }

    public function getRequest(): ArchitectureCqrsRequestInterface
    {
        return $this->findOneByFilterQueryCase;
    }
}
