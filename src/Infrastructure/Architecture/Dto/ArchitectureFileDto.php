<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto;

use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\Collection;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ConstructorDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocDescriptionDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\FieldDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\MethodDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;

class ArchitectureFileDto
{
    private string $namespace;
    private string $className;
    private ClassTypeEnum $classType;
    private CollectionInterface $useNamespaceCollection;
    private CollectionInterface $classCommentCollection;
    private CollectionInterface $implementsCollection;
    private CollectionInterface $extendsCollection;
    private CollectionInterface $useTraitCollection;
    private CollectionInterface $constantCollection;
    private CollectionInterface $fieldCollection;
    private CollectionInterface $methodCollection;
    private CollectionInterface $docCollection;
    private ?DocDescriptionDto $description;
    private ?ConstructorDto $constructor;
    private bool $isOverrideExistFile;

    public function __construct()
    {
        $collection = new Collection();
        $this->useNamespaceCollection = new Collection();
        $this->classCommentCollection = new Collection();
        $this->implementsCollection = new Collection();
        $this->useTraitCollection = new Collection();
        $this->constantCollection = new Collection();
        $this->fieldCollection = new Collection();
        $this->extendsCollection = new Collection();
        $this->methodCollection = new Collection();
        $this->docCollection = new Collection();
        $this->description = null;
        $this->constructor = null;
        $this->namespace = '';
        $this->className = '';
        $this->classType = ClassTypeEnum::CLASS_;
        $this->isOverrideExistFile = true;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getClassType(): ClassTypeEnum
    {
        return $this->classType;
    }

    public function getUseNamespaceCollection(): CollectionInterface
    {
        return $this->useNamespaceCollection;
    }

    public function getClassCommentCollection(): CollectionInterface
    {
        return $this->classCommentCollection;
    }

    public function getImplementsCollection(): CollectionInterface
    {
        return $this->implementsCollection;
    }

    public function getExtendsCollection(): CollectionInterface
    {
        return $this->extendsCollection;
    }

    public function getUseTraitCollection(): CollectionInterface
    {
        return $this->useTraitCollection;
    }

    public function getConstantCollection(): CollectionInterface
    {
        return $this->constantCollection;
    }

    /**
     * @return CollectionInterface|FieldDto[]
     */
    public function getFieldCollection(): CollectionInterface
    {
        return $this->fieldCollection;
    }

    /**
     * @return CollectionInterface|MethodDto[]
     */
    public function getMethodCollection(): CollectionInterface
    {
        return $this->methodCollection;
    }

    public function setNamespace(string $namespace): static
    {
        $this->namespace = $namespace;
        return $this;
    }

    public function setClassName(string $className): static
    {
        $this->className = $className;
        return $this;
    }

    public function setClassType(ClassTypeEnum $classType): static
    {
        $this->classType = $classType;
        return $this;
    }

    public function setUseNamespaceCollection(CollectionInterface $useNamespaceCollection): static
    {
        $this->useNamespaceCollection = $useNamespaceCollection;
        return $this;
    }

    public function setClassCommentCollection(CollectionInterface $classCommentCollection): static
    {
        $this->classCommentCollection = $classCommentCollection;
        return $this;
    }

    public function setImplementsCollection(CollectionInterface $implementsCollection): static
    {
        $this->implementsCollection = $implementsCollection;
        return $this;
    }

    public function setExtendsCollection(CollectionInterface $extendsCollection): static
    {
        $this->extendsCollection = $extendsCollection;
        return $this;
    }

    public function setUseTraitCollection(CollectionInterface $useTraitCollection): static
    {
        $this->useTraitCollection = $useTraitCollection;
        return $this;
    }

    public function setConstantCollection(CollectionInterface $constantCollection): static
    {
        $this->constantCollection = $constantCollection;
        return $this;
    }

    public function setFieldCollection(CollectionInterface $fieldCollection): static
    {
        $this->fieldCollection = $fieldCollection;
        return $this;
    }

    public function setMethodCollection(CollectionInterface $methodCollection): static
    {
        $this->methodCollection = $methodCollection;
        return $this;
    }

    /**
     * @return CollectionInterface|DocDto[]
     */
    public function getDocCollection(): CollectionInterface
    {
        return $this->docCollection;
    }

    public function setDocCollection(CollectionInterface $docCollection): static
    {
        $this->docCollection = $docCollection;
        return $this;
    }

    public function getDescription(): ?DocDescriptionDto
    {
        return $this->description;
    }

    public function setDescription(?DocDescriptionDto $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getConstructor(): ?ConstructorDto
    {
        return $this->constructor;
    }

    public function setConstructor(?ConstructorDto $constructor): static
    {
        $this->constructor = $constructor;
        return $this;
    }

    public function isOverrideExistFile(): bool
    {
        return $this->isOverrideExistFile;
    }

    public function setIsOverrideExistFile(bool $isOverrideExistFile): static
    {
        $this->isOverrideExistFile = $isOverrideExistFile;

        return $this;
    }
}
