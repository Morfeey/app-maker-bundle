<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Handler\Query\DefaultQuery;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Query\DefaultQuery\FindByFilterFieldListQueryHandlerInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Handler\DefaultHandler;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\RepositoryInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\ConstructorCreatorService;

class FindByFilterFieldListQueryHandlerCase extends DefaultHandler implements ArchitectureFileCaseInterface
{

    public function __construct(
        protected readonly AttributesCreatorFacade $attributesCreatorFacade,
        protected readonly ConstructorCreatorService $constructorCreatorService,
        protected readonly FindByFilterFieldListQueryHandlerInterfaceCase $handlerInterfaceCase,
        protected readonly RepositoryInterfaceCase $repositoryInterfaceCase
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        return $this->createDefaultCase(
            $caseParameters,
            $this->handlerInterfaceCase,
            $this->attributesCreatorFacade,
            $this->repositoryInterfaceCase
        );
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto
    {
        return $this->createDefaultUse($this->attributesCreatorFacade, $fileCase, $isArray);
    }
}
