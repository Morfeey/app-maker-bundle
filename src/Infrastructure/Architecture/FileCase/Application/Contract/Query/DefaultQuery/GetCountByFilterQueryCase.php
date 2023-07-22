<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Query\DefaultQuery;

use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\ArchitectureCqrsRequestInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\DefaultCqrsRequestCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\NonArchitect\Enum\RequestCqrsType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;

class GetCountByFilterQueryCase extends DefaultCqrsRequestCase implements ArchitectureFileCaseInterface, ArchitectureCqrsRequestInterface
{

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        $filterParameter = $this->createFilterDependency($caseParameters);

        return
            parent::create($caseParameters)
                ->setConstructor($this->createConstructorByParameters(null, $filterParameter))
                ->setMethodCollection($this->createGettersByParameters($filterParameter));
    }

    public function createDependencies(FileCase $caseParameters): CollectionInterface
    {
        return $this->createCollection()->add($this->createFilterDependency($caseParameters));
    }

    public function createParentDependencies(FileCase $caseParameters): CollectionInterface
    {
        return $this->createCollection()->add($this->createParentFilterDependency());
    }

    public function getType(): RequestCqrsType
    {
        return RequestCqrsType::QUERY;
    }
}
