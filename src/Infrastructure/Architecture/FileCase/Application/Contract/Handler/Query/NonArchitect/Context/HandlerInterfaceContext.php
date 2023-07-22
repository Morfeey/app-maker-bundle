<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Query\NonArchitect\Context;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\HandlerInterfaceCaseInterface;

class HandlerInterfaceContext
{
    public function __construct(protected readonly iterable $handlerInterfaceCases)
    {
    }

    /**
     * @return iterable|HandlerInterfaceCaseInterface[]
     */
    public function create(): iterable
    {
        foreach ($this->handlerInterfaceCases as $case) {
            yield $case;
        }
    }
}
