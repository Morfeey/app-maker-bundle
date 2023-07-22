<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Command\DefaultCommand;

use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\ArchitectureCqrsRequestInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\DefaultCqrsRequestCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Filter\Entity\FieldListInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Filter\FilterInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\NonArchitect\Enum\RequestCqrsType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\ConstructorCreatorService;

class UpdateByFilterCommandCase extends DefaultCqrsRequestCase implements ArchitectureFileCaseInterface, ArchitectureCqrsRequestInterface
{
    public function __construct(
        AttributesCreatorFacade $attributesCreatorFacade,
        FilterInterfaceCase $filterInterfaceCase,
        FieldListInterfaceCase $fieldListInterfaceCase,
        private readonly ConstructorCreatorService $constructorCreatorService
    ) {
        parent::__construct($attributesCreatorFacade, $filterInterfaceCase, $fieldListInterfaceCase);
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        $filterParameter = $this->createFilterDependency($caseParameters);
        $fieldParameter = $this->createFieldDependency($caseParameters, true, true);
        $fieldFields = $this->attributesCreatorFacade->createFieldByUse(
            self::FIELDS_NAME,
            $this->fieldListInterfaceCase->createUse($caseParameters),
            true
        );

        $constructor = $this->createConstructorByParameters(
            $this->constructorCreatorService->createContentByFields($fieldFields),
            $filterParameter,
            $fieldParameter
        );

        return parent::create($caseParameters)
            ->setConstructor($constructor)
            ->setFieldCollection($this->createCollection()->add($fieldFields))
            ->setMethodCollection($this->createGettersByParameters($filterParameter, $fieldParameter));
    }

    public function createDependencies(FileCase $caseParameters): CollectionInterface
    {
        return $this->createCollection()
            ->add($this->createFilterDependency($caseParameters))
            ->add($this->createFieldDependency($caseParameters, true, true));
    }

    public function createParentDependencies(FileCase $caseParameters): CollectionInterface
    {
        return $this->createCollection()
            ->add($this->createParentFilterDependency())
            ->add($this->createParentFieldDependency(true, true));
    }

    public function getType(): RequestCqrsType
    {
        return RequestCqrsType::COMMAND;
    }
}
