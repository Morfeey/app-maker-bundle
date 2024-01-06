<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Query\DefaultQuery;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\ArchitectureCqrsRequestInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\DefaultCqrsRequestCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\NonArchitect\Enum\RequestCqrsType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;

class FindAllIterableQueryCase extends DefaultCqrsRequestCase implements ArchitectureFileCaseInterface, ArchitectureCqrsRequestInterface
{
    public function createDependencies(FileCase $caseParameters): CollectionInterface
    {
        return $this->createCollection();
    }

    public function createParentDependencies(FileCase $caseParameters): CollectionInterface
    {
        return $this->createCollection();
    }

    public function getType(): RequestCqrsType
    {
        return RequestCqrsType::QUERY;
    }
}