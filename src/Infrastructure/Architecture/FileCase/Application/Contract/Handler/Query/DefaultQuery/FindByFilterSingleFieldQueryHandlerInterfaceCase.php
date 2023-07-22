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
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Query\DefaultQuery\FindByFilterSingleFieldQueryCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;

class FindByFilterSingleFieldQueryHandlerInterfaceCase extends DefaultHandlerInterfaceCase implements ArchitectureFileCaseInterface, HandlerInterfaceCaseInterface
{

    public function __construct(
        protected readonly AttributesCreatorFacade $attributesCreatorFacade,
        protected readonly FindByFilterSingleFieldQueryCase $query
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        $invoke = $this->createInvokeMethod(
            $this->attributesCreatorFacade,
            $caseParameters,
            $this->query,
            'mixed'
        );

        return
            $this->createDefault($caseParameters)
                ->setClassType(ClassTypeEnum::INTERFACE_)
                ->setMethodCollection($this->createCollection()->add($invoke));
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto
    {
        return $this->createDefaultUse($this->attributesCreatorFacade, $fileCase, $isArray);
    }

    public function getRequest(): ArchitectureCqrsRequestInterface
    {
        return $this->query;
    }
}
