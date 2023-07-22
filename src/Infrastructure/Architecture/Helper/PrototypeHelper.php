<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\StringValuePrototypeTrait;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\Collection;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;

trait PrototypeHelper
{
    use StringValuePrototypeTrait;
    protected CollectionInterface $collection;
    public function createCollection(): CollectionInterface
    {
        $this->collection = $this->collection ?? new Collection();

        return clone $this->collection;
    }
}
