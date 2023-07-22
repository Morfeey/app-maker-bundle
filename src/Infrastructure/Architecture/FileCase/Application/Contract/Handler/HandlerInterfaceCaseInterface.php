<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\ArchitectureCqrsRequestInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;

interface HandlerInterfaceCaseInterface extends ArchitectureFileCaseInterface
{
    public function getRequest(): ArchitectureCqrsRequestInterface;
}
