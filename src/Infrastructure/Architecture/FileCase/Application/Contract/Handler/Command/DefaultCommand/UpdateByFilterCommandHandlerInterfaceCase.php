<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Command\DefaultCommand;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\ArchitectureCqrsRequestInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Command\DefaultCommand\UpdateByFilterCommandCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\DefaultHandlerInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\HandlerInterfaceCaseInterface;

class UpdateByFilterCommandHandlerInterfaceCase extends DefaultHandlerInterfaceCase implements HandlerInterfaceCaseInterface
{
    public function __construct(
        private readonly AttributesCreatorFacade $attributesCreatorFacade,
        private readonly UpdateByFilterCommandCase $updateCommandCase
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        $invoke = $this->createInvokeMethod($this->attributesCreatorFacade, $caseParameters, $this->updateCommandCase, 'static');

        return
            $this->createDefault($caseParameters)
                ->setClassType(ClassTypeEnum::INTERFACE_)
                ->setMethodCollection($this->createCollection()->add($invoke))
                ->setUseNamespaceCollection($this->createCollection()->add(
                    $this->updateCommandCase->createUse($caseParameters)
                ));
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto
    {
        return $this->createDefaultUse($this->attributesCreatorFacade, $fileCase, $isArray);
    }

    public function getRequest(): ArchitectureCqrsRequestInterface
    {
        return $this->updateCommandCase;
    }
}
