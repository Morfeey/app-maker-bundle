<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Handler\Command\DefaultCommand;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Command\DefaultCommand\CreateCommandHandlerInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\HandlerInterfaceCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Factory\CommandQueryDtoToEntityFactoryInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Handler\DefaultHandler;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\RepositoryInterfaceCase;

class CreateCommandHandlerCase extends DefaultHandler implements ArchitectureFileCaseInterface
{
    public const FACTORY_METHOD_NAME = 'mapping';
    public function __construct(
        private readonly AttributesCreatorFacade $attributesCreatorFacade,
        private readonly CreateCommandHandlerInterfaceCase $handlerInterfaceCase,
        private readonly CommandQueryDtoToEntityFactoryInterfaceCase $factoryCase,
        private readonly RepositoryInterfaceCase $repositoryInterfaceCase
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

    protected function createContentInvoke(HandlerInterfaceCaseInterface $handlerInterfaceCase, FileCase $caseParameters, bool $issetFactoryDependency = false): string
    {
        $contentLineList = [
            '$entity = $this->factory->mapping($command->getCommandQueryDto());',
            '        $this->repository->save($entity);',
            '',
            '        return $this;'
        ];

        return implode(PHP_EOL, $contentLineList);
    }
}
