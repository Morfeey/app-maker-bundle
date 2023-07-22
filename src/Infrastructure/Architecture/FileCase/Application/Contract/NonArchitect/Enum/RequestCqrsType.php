<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\NonArchitect\Enum;

enum RequestCqrsType
{
    case QUERY;
    case COMMAND;

    public function getValue(): string
    {
        return strtolower($this->name);
    }
}
