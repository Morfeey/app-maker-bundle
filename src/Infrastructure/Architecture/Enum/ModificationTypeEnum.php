<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\StringValueEnumInterface;

enum ModificationTypeEnum: string implements StringValueEnumInterface
{
    case PUBLIC_ = 'public';
    case PRIVATE_ = 'private';
    case PROTECTED_ = 'protected';

    public function getValue(): string
    {
        return $this->value;
    }
}
