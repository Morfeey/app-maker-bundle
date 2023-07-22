<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Service;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ParameterDto as Parameter;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\DocTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\MethodTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;
use ReflectionMethod;

class DocMethodInterfaceCreatorService
{
    use PrototypeHelper;
    public function __construct(
        protected readonly AttributesCreatorFacade $attributesCreatorFacade
    ) {
    }

    public function createByMethod(ReflectionMethod $method, Parameter ...$parameters): DocDto
    {
        return $this->attributesCreatorFacade->createDoc(
            DocTypeEnum::METHOD,
            $this->attributesCreatorFacade->createMethod(
                $method->getName(),
                '',
                $this->createCollection()->setItems($parameters),
                MethodTypeEnum::NON_STATIC,
                ModificationTypeEnum::PUBLIC_,
                true
            )
        );
    }
}
