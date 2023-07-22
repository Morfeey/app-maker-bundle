<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Service;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DefaultValue;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ParameterDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\TypeDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\DefaultValueCreatorService;
use ReflectionParameter;

class DocMethodParameterInterfaceCreatorService
{
    public function __construct(
        protected readonly AttributesCreatorFacade $attributesCreatorFacade,
        protected readonly DefaultValueCreatorService $defaultValueCreatorService
    ) {
    }

    public function create(ReflectionParameter $parameter, TypeDto $type, ?DefaultValue $defaultValue = null): ParameterDto
    {
        return $this->attributesCreatorFacade->createParameter(
            $parameter->getName(),
            $type,
            $defaultValue,
            null,
            false
        );
    }
}
