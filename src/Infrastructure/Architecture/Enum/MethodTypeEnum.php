<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\StringValueEnumInterface;

enum MethodTypeEnum: string implements StringValueEnumInterface
{
    case STATIC_ = 'static';
    case NON_STATIC = '';

    public function getValue(): string
    {
        return $this->value;
    }
}
