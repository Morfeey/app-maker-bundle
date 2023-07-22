<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Constructor\Enum;

enum ConstructorTypeEnum
{
    case DTO;
    case DTO_WITH_NULLABLE_ID;
}
