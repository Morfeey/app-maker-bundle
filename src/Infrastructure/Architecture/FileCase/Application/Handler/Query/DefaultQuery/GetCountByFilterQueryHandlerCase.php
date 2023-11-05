<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Handler\Query\DefaultQuery;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Query\DefaultQuery\GetCountByFilterQueryHandlerInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Handler\DefaultHandler;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\RepositoryInterfaceCase;

class GetCountByFilterQueryHandlerCase extends DefaultHandler implements ArchitectureFileCaseInterface
{

    public function __construct(
        private readonly AttributesCreatorFacade $attributesCreatorFacade,
        private readonly GetCountByFilterQueryHandlerInterfaceCase $handlerInterfaceCase,
        private readonly RepositoryInterfaceCase $repositoryInterfaceCase
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
