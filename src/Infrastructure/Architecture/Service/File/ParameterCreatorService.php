<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DefaultValue;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ParameterDto as Parameter;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\TypeDto as Type;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto as UseNamespace;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum as ModificationType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;

class ParameterCreatorService
{
    use PrototypeHelper;
    public function create(
        string $name,
        Type $type,
        ?DefaultValue $defaultValue = null,
        ?ModificationType $modificationType = null,
        bool $isReadonly = false,
        UseNamespace ...$usesNamespace
    ): Parameter {
        $useCollection = $this->createCollection();
        foreach ($usesNamespace as $useNamespace) {
            if ($useNamespace) {
                $useCollection->add($useNamespace);
            }
        }

        return new Parameter($name, $type, $defaultValue, $modificationType, $isReadonly, $useCollection);
    }

    public function createByUse(string $name, UseNamespace $use): Parameter
    {
        return $this->create();
    }
}
