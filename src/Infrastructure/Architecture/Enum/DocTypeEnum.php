<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\StringValueEnumInterface;

enum DocTypeEnum implements StringValueEnumInterface
{
    case METHOD;
    case PROPERTY;
    case PARAM;
    case RETURN;

    public function getValue(): string
    {
        return strtolower($this->name);
    }
}
