<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DefaultValue;

class DefaultValueCreatorService
{
    public function create(mixed $value, ?string $valueContent = null): DefaultValue
    {
        return new DefaultValue($value, $valueContent);
    }
}
