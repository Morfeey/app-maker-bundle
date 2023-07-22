<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\TypeDto as Type;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;

class TypeCreatorService
{
    public function create(
        bool $isVariadic,
        bool $isObject,
        ?string $type = null,
        ?UseNamespaceDto $useNamespaceDto = null
    ): Type {
        return new Type($isVariadic, $isObject, $type, $useNamespaceDto);
    }
}
