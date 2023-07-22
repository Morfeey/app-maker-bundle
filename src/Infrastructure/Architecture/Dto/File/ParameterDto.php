<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DefaultValue;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocTypeInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\TypeDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\Collection;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum;

class ParameterDto implements DocTypeInterface
{
    public function __construct(
        protected readonly string $name,
        protected readonly TypeDto $type,
        protected readonly ?DefaultValue $value = null,
        protected readonly ?ModificationTypeEnum $modificationType = null,
        protected readonly bool $isReadonly = false,
        protected ?CollectionInterface $useCollection = null
    ) {
        $this->useCollection = !$useCollection ? new Collection() : $useCollection;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): TypeDto
    {
        return $this->type;
    }

    public function getValue(): ?DefaultValue
    {
        return $this->value;
    }

    public function getModificationType(): ?ModificationTypeEnum
    {
        return $this->modificationType;
    }

    public function isReadonly(): bool
    {
        return $this->isReadonly;
    }

    /**
     * @return CollectionInterface<int, UseNamespaceDto>|UseNamespaceDto[]|null
     */
    public function getUseCollection(): ?CollectionInterface
    {
        return $this->useCollection;
    }
}
