<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\StringValueEnumInterface;

enum ClassTypeEnum: string implements StringValueEnumInterface
{
    case CLASS_ = 'class';
    case INTERFACE_ = 'interface';
    case TRAIT_ = 'trait';
    case ENUM_ = 'enum';
    case ABSTRACT_CLASS = 'abstract class';
    case FINAL_CLASS_ = 'final class';

    public function getValue(): string
    {
        return $this->value;
    }
}
