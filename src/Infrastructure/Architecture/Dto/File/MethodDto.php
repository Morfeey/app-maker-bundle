<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocDescriptionDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocTypeInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ParameterDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\Collection;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Enum\MethodCaseEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\MethodTypeEnum as MethodType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum as ModificationType;

class MethodDto implements DocTypeInterface
{
    private CollectionInterface $docCollection;
    private ?DocDescriptionDto $description;
    public function __construct(
        protected readonly CollectionInterface $parameters,
        protected readonly string $typeHint,
        protected readonly string $name,
        protected readonly MethodType $type = MethodType::NON_STATIC,
        protected readonly ModificationType $modificationType = ModificationType::PUBLIC_,
        protected readonly bool $isVirtual = false,
        protected readonly ?string $content = null,
        protected readonly MethodCaseEnum $case = MethodCaseEnum::GETTER,
        protected ?CollectionInterface $useCollection = null
    ) {
        $this->useCollection = !$useCollection ? new Collection() : $useCollection;
        $this->docCollection = new Collection();
        $this->description = null;
    }

    /**
     * @return CollectionInterface|ParameterDto[]
     */
    public function getParameters(): CollectionInterface
    {
        return $this->parameters;
    }

    public function getTypeHint(): string
    {
        return $this->typeHint;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): MethodType
    {
        return $this->type;
    }

    public function getModificationType(): ModificationType
    {
        return $this->modificationType;
    }

    public function isVirtual(): bool
    {
        return $this->isVirtual;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return CollectionInterface<int, UseNamespaceDto>|UseNamespaceDto[]|null
     */
    public function getUseCollection(): ?CollectionInterface
    {
        return $this->useCollection;
    }

    public function getCase(): MethodCaseEnum
    {
        return $this->case;
    }

    public function getDocCollection(): CollectionInterface|Collection
    {
        return $this->docCollection;
    }

    public function setDocCollection(CollectionInterface|Collection $docCollection): static
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
}
