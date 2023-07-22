<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DefaultValue;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\FieldDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\TypeDto as Type;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto as UseNamespace;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum as ModificationType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;

class FieldCreatorService
{
    use PrototypeHelper;
    public function create(
        string $name,
        Type $type,
        ?DefaultValue $defaultValue = null,
        ?ModificationType $modificationType = null,
        bool $isReadonly = false,
        UseNamespace ...$usesNamespace
    ): FieldDto {
        $uses = $this->createCollection();
        foreach ($usesNamespace as $use) {
            $uses->add($use);
        }

        return new FieldDto($name, $type, $defaultValue, $modificationType, $isReadonly, $uses);
    }
}
