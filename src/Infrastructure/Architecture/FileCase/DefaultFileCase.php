<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\CreatorDefaultArchitectureFileDtoHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\NamespaceHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;

abstract class DefaultFileCase implements ArchitectureFileCaseInterface
{
    use NamespaceHelper, CreatorDefaultArchitectureFileDtoHelper, PrototypeHelper;
    public function createDefaultUse(
        AttributesCreatorFacade $attributesCreatorFacade,
        FileCase $fileCase,
        bool $isArray = false
    ): UseNamespaceDto {
        $fileCase = $attributesCreatorFacade->createCaseDtoByCase($fileCase, $this);
        $file = $this->createDefault($fileCase);

        return
            $attributesCreatorFacade->createUse(
                $this->mergeNamesapce($file->getNamespace(), $file->getClassName()),
                $file->getClassName(),
                null,
                $isArray
            );
    }
}
