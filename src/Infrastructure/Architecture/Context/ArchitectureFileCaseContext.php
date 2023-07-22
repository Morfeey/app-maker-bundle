<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Context;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use Generator;

class ArchitectureFileCaseContext
{
    public function __construct(private readonly iterable $architectureFileCase)
    {
    }

    public function create(): ArchitectureFileCaseInterface|Generator
    {
        /** @var ArchitectureFileCaseInterface $case */
        foreach ($this->architectureFileCase as $case) {
            yield $case;
        }
    }
}
