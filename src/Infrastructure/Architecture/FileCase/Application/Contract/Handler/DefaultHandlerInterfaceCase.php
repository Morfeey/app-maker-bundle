<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\MethodDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\MethodTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\ArchitectureCqrsRequestInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\DefaultFileCase;

abstract class DefaultHandlerInterfaceCase extends DefaultFileCase
{
    public function createInvokeMethod(
        AttributesCreatorFacade          $attributesCreatorFacade,
        ArchitectureFileCaseDto          $caseParameters,
        ArchitectureCqrsRequestInterface $query,
        string                           $typeHint
    ): MethodDto {
        return $attributesCreatorFacade->createMethod(
            '__invoke',
            $typeHint,
            $this->createCollection()->add(
                $attributesCreatorFacade->createParameterByUse(
                    $query->getType()->getValue(),
                    $query->createUse($caseParameters),
                    false,
                    null,
                    null,
                    false
                )
            ),
            MethodTypeEnum::NON_STATIC,
            ModificationTypeEnum::PUBLIC_,
            true
        );
    }
}
