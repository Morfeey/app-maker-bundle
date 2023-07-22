<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Command\DefaultCommand;

use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ParameterDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\ArchitectureCqrsRequestInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\DefaultCqrsRequestCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Filter\Entity\FieldListInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Filter\FilterInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Command\Dto\CommandQueryDtoCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\NonArchitect\Enum\RequestCqrsType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;

class CreateCommandCase extends DefaultCqrsRequestCase implements ArchitectureFileCaseInterface, ArchitectureCqrsRequestInterface
{
    public function __construct(
        AttributesCreatorFacade $attributesCreatorFacade,
        FilterInterfaceCase $filterInterfaceCase,
        FieldListInterfaceCase $fieldListInterfaceCase,
        private readonly CommandQueryDtoCase $commandQueryDtoCase
    ) {
        parent::__construct($attributesCreatorFacade, $filterInterfaceCase, $fieldListInterfaceCase);
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        return
            parent::create($caseParameters)
                ->setConstructor($this->createConstructorByParameters(
                    null,
                    $this->createCommandQueryDtoDependency($caseParameters, true)
                ))
                ->setMethodCollection(
                    $this->createGettersByParameters(...$this->createDependencies($caseParameters)->toArray())
                );
    }

    public function createDependencies(FileCase $caseParameters): CollectionInterface
    {
        return $this->createCollection()->add($this->createCommandQueryDtoDependency($caseParameters));
    }

    public function createParentDependencies(FileCase $caseParameters): CollectionInterface
    {
        return $this->createCollection()->add($this->createCommandQueryDtoDependency($caseParameters));
    }

    public function getType(): RequestCqrsType
    {
        return RequestCqrsType::COMMAND;
    }

    private function createCommandQueryDtoDependency(FileCase $caseParameters, bool $isConstructor = false): ParameterDto
    {
        $commandQueryDtoCase = $this->commandQueryDtoCase->createUse($caseParameters);

        return $this->attributesCreatorFacade->createParameterByUse(
            'commandQueryDto',
            $commandQueryDtoCase,
            false,
            $isConstructor ? ModificationTypeEnum::PRIVATE_ : null,
            null,
            $isConstructor
        );
    }
}
