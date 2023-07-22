<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocTypeInterface;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\DocTypeEnum as Type;

class DocDto
{
    public function __construct(
        protected CollectionInterface $useCollection,
        protected readonly Type $type,
        protected DocTypeInterface $entity
    ) {
    }

    public function getUseCollection(): CollectionInterface
    {
        return $this->useCollection;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getEntity(): DocTypeInterface
    {
        return $this->entity;
    }
}
