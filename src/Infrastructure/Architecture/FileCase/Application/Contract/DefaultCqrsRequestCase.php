<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\ArchitectureCqrsRequestInterface;
use App\Bundles\InfrastructureBundle\Application\Contract\Command\CommandInterface;
use App\Bundles\InfrastructureBundle\Application\Contract\Filter\FieldList\ContractEntityFieldListInterface;
use App\Bundles\InfrastructureBundle\Application\Contract\Filter\FilterInterface;
use App\Bundles\InfrastructureBundle\Application\Contract\Query\QueryInterface;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ConstructorDto as Constructor;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ParameterDto as Parameter;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Filter\Entity\FieldListInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Filter\FilterInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\NonArchitect\Enum\RequestCqrsType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\DefaultFileCase;

abstract class DefaultCqrsRequestCase extends DefaultFileCase implements ArchitectureFileCaseInterface, ArchitectureCqrsRequestInterface
{
    public const FILTER_NAME = 'filter';
    public const FIELDS_NAME = 'fields';
    public const FIELD_NAME = 'field';

    public function __construct(
        protected AttributesCreatorFacade $attributesCreatorFacade,
        protected FilterInterfaceCase $filterInterfaceCase,
        protected FieldListInterfaceCase $fieldListInterfaceCase
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        $defaultInterface = QueryInterface::class;
        if ($this->getType() === RequestCqrsType::COMMAND) {
            $defaultInterface = CommandInterface::class;
        }

        return
            $this->createDefault($caseParameters)
                ->setClassType(ClassTypeEnum::CLASS_)
                ->setImplementsCollection(
                    $this->createCollection()
                        ->add($this->attributesCreatorFacade->createUse($defaultInterface))
                );
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto
    {
        return $this->createDefaultUse($this->attributesCreatorFacade, $fileCase, $isArray);
    }

    public function createGettersByParameters(Parameter ...$parameters): CollectionInterface
    {
        $gettersCollection = $this->createCollection();
        foreach ($parameters as $parameter) {
            $gettersCollection->add($this->attributesCreatorFacade->createGetterByParameter($parameter));
        }

        return $gettersCollection;
    }

    protected function createConstructorByParameters(?string $content = null, Parameter ...$parameters): Constructor
    {
        return $this->attributesCreatorFacade->createConstructor($content, ...$parameters);
    }

    protected function createParentFilterDependency(): Parameter
    {
        return $this->attributesCreatorFacade->createParameterByUse(
            self::FILTER_NAME,
            $this->attributesCreatorFacade->createUse(FilterInterface::class)
        );
    }

    protected function createFilterDependency(FileCase $caseParameters): Parameter
    {
        return $this->attributesCreatorFacade->createParameterByUse(
            self::FILTER_NAME,
            $this->filterInterfaceCase->createUse($caseParameters)
        );
    }

    protected function createFieldDependency(FileCase $caseParameters, bool $isMany = false, bool $isVariadic = false): Parameter
    {
        return $this->attributesCreatorFacade->createParameterByUse(
            !$isMany ? self::FIELD_NAME : self::FIELDS_NAME,
            $this->fieldListInterfaceCase->createUse($caseParameters),
            $isVariadic,
            null,
            null,
            !$isVariadic
        );
    }

    protected function createParentFieldDependency(bool $isMany = false, bool $isVariadic = false): Parameter
    {
        return $this->attributesCreatorFacade->createParameterByUse(
            !$isMany ? self::FIELD_NAME : self::FIELDS_NAME,
            $this->attributesCreatorFacade->createUse(ContractEntityFieldListInterface::class),
            $isVariadic,
            null,
            null,
            !$isVariadic
        );
    }
}
