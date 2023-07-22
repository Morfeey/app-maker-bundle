<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Entity;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\DefaultFileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\CreatorDefaultArchitectureFileDtoHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\NamespaceHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;

abstract class DefaultDomainEntityInterfaceCase extends DefaultFileCase implements ArchitectureFileCaseInterface
{
    use NamespaceHelper, CreatorDefaultArchitectureFileDtoHelper, PrototypeHelper;
    public function __construct(
        protected readonly AttributesCreatorFacade $attributesCreator
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        return $this
            ->createDefault($caseParameters)
            ->setClassType(ClassTypeEnum::INTERFACE_);
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto
    {
        return $this->createDefaultUse($this->attributesCreator, $fileCase, $isArray);
    }
}
