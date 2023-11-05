<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Infrastructure\Doctrine\Filter\Entity;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\MethodDto;
use App\Bundles\InfrastructureBundle\Infrastructure\Doctrine\Entity\CustomEntityInterface;
use App\Bundles\InfrastructureBundle\Infrastructure\Doctrine\Entity\FieldList\DefaultFieldList;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\MethodTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Filter\Entity\FieldListInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\CreatorDefaultArchitectureFileDtoHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\NamespaceHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\TypeCreatorService;
use ReflectionProperty;

class DoctrineFieldListCase implements ArchitectureFileCaseInterface
{

    use PrototypeHelper, CreatorDefaultArchitectureFileDtoHelper, NamespaceHelper;
    public function __construct(
        protected readonly AttributesCreatorFacade $attributesCreatorFacade,
        protected readonly TypeCreatorService $typeCreatorService,
        protected readonly FieldListInterfaceCase $fieldListInterfaceCase
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        $constantList = [];
        $methods = [];
        foreach ($caseParameters->getReflectionEntity()->getProperties() as $property) {
            $methods[] = $this->attributesCreatorFacade->createMethod(
                $property->getName(),
                'static',
                $this->createCollection(),
                MethodTypeEnum::NON_STATIC,
                ModificationTypeEnum::PUBLIC_,
                false,
                $this->createMethodContent($property, $caseParameters->getEntity())
            );
            $constantList[$this->createConstantName($property)] = $property->getName();
        }

        $methods[] = $this->attributesCreatorFacade->createMethod(
            'getList',
            'array',
            $this->createCollection(),
            MethodTypeEnum::NON_STATIC,
            ModificationTypeEnum::PUBLIC_,
            false,
            $this->createMethodGetListContent(array_map(static fn(MethodDto $methodDto) => $methodDto->getName(), $methods))
        );

        foreach ($constantList as $constKey => $value) {
            print "public const {$constKey} = '{$value}';\n";
        }

        return
            $this->createDefault($caseParameters)
                ->setMethodCollection($this->createCollection()->setItems($methods))
                ->setUseNamespaceCollection($this->createCollection()->add($this->attributesCreatorFacade->createUse(get_class($caseParameters->getEntity()))))
                ->setExtendsCollection(
                    $this->createCollection()
                        ->add($this->attributesCreatorFacade->createUse(DefaultFieldList::class))
                )->setImplementsCollection(
                    $this->createCollection()
                        ->add($this->fieldListInterfaceCase->createUse($caseParameters))
                );
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto
    {
        $fileCase = $this->attributesCreatorFacade->createCaseDtoByCase($fileCase, $this);

        return $this->attributesCreatorFacade->createUse(
            $fileCase->getCaseDto()->getNamespace(),
            null,
            null,
            $isArray
        );
    }

    protected function createMethodContent(ReflectionProperty $property, CustomEntityInterface $entity): string
    {
        $className = $this->getClassNameByNamespace(get_class($entity));

        return "return self::create({$className}::{$this->createConstantName($property)});";
    }

    /**
     * @param string[] $methodNames
     * @return string
     */
    private function createMethodGetListContent(array $methodNames): string
    {
        $contentLineList = [
            'return [',
        ];

        foreach($methodNames as $methodName) {
            $contentLineList[] = "           self::{$methodName}(),";
        }

        $contentLineList[] = '       ];';


        return implode(PHP_EOL, $contentLineList);
    }

    public function createConstantName(ReflectionProperty $property): string
    {
        $snakeName =
            $this->createPrototypeStringValue()
                ->setString($property->getName())
                ->toSnakeCase()
                ->toUp()
                ->getResult();
        return "C_{$snakeName}";
    }
}
