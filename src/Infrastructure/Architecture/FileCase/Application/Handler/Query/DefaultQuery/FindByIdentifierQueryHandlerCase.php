<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Handler\Query\DefaultQuery;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Query\DefaultQuery\FindByIdentifierQueryHandlerInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Factory\DomainToQueryResponseFactoryInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Handler\DefaultHandler;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\RepositoryInterfaceCase;

class FindByIdentifierQueryHandlerCase extends DefaultHandler implements ArchitectureFileCaseInterface
{

    public const FACTORY_METHOD_NAME = 'mapping';
    public function __construct(
        private readonly AttributesCreatorFacade $attributesCreatorFacade,
        private readonly FindByIdentifierQueryHandlerInterfaceCase $handlerInterfaceCase,
        private readonly RepositoryInterfaceCase $repositoryInterfaceCase,
        private readonly DomainToQueryResponseFactoryInterfaceCase $factoryCase
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        return $this->createDefaultCase(
            $caseParameters,
            $this->handlerInterfaceCase,
            $this->attributesCreatorFacade,
            $this->repositoryInterfaceCase,
            $this->factoryCase
        );
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto
    {
        return $this->createDefaultUse($this->attributesCreatorFacade, $fileCase, $isArray);
    }
}
