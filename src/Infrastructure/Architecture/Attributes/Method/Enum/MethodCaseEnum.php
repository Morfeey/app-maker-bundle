<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Enum;

enum MethodCaseEnum
{
    case GETTER_AND_NULLABLE_ID;
    case GETTER;
    case SETTER;
}
