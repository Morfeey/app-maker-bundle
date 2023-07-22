<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ParameterDto;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;

class ConstructorDto
{
    public function __construct(
        protected readonly CollectionInterface $dependencies,
        protected readonly ?string $content = null
    ) {
    }

    /**
     * @return CollectionInterface|ParameterDto[]
     */
    public function getDependencies(): CollectionInterface
    {
        return $this->dependencies;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }
}
