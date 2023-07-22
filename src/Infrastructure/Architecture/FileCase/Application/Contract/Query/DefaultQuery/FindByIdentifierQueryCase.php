<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Query\DefaultQuery;

use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\ArchitectureCqrsRequestInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\DefaultCqrsRequestCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\NonArchitect\Enum\RequestCqrsType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;

class FindByIdentifierQueryCase extends DefaultCqrsRequestCase implements ArchitectureFileCaseInterface, ArchitectureCqrsRequestInterface
{
    public function create(FileCase $caseParameters): ArchitectureFile
    {
        return parent::create($caseParameters)
            ->setConstructor($this->attributesCreatorFacade->createConstructor(null, $this->createDependencies($caseParameters)->first()))
            ->setMethodCollection($this->createGettersByParameters($this->createDependencies($caseParameters)->first()));
    }

    public function createDependencies(FileCase $caseParameters): CollectionInterface
    {
        $idDependency = $this->attributesCreatorFacade->createParameter(
            'id',
            $this->attributesCreatorFacade->createType('mixed'),
            null,
            ModificationTypeEnum::PRIVATE_,
            true
        );

        return $this->createCollection()->add($idDependency);
    }

    public function createParentDependencies(FileCase $caseParameters): CollectionInterface
    {
        return $this->createDependencies($caseParameters);
    }

    public function getType(): RequestCqrsType
    {
        return RequestCqrsType::QUERY;
    }
}
