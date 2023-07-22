<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Command\DefaultCommand;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\ArchitectureCqrsRequestInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Command\DefaultCommand\DeleteByFilterCommandCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\DefaultHandlerInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\HandlerInterfaceCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;

class DeleteByFilterCommandHandlerInterfaceCase extends DefaultHandlerInterfaceCase implements ArchitectureFileCaseInterface, HandlerInterfaceCaseInterface
{

    public function __construct(
        protected readonly AttributesCreatorFacade $attributesCreatorFacade,
        protected readonly DeleteByFilterCommandCase $queryCase
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        $invoke = $this->createInvokeMethod($this->attributesCreatorFacade, $caseParameters, $this->queryCase, 'static');

        return
            $this->createDefault($caseParameters)
                ->setClassType(ClassTypeEnum::INTERFACE_)
                ->setMethodCollection($this->createCollection()->add($invoke))
                ->setUseNamespaceCollection($this->createCollection()->add($this->queryCase->createUse($caseParameters)));
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto
    {
        return $this->createDefaultUse($this->attributesCreatorFacade, $fileCase, $isArray);
    }

    public function getRequest(): ArchitectureCqrsRequestInterface
    {
        return $this->queryCase;
    }
}
