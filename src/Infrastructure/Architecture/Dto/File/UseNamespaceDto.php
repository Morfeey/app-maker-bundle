<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File;

class UseNamespaceDto
{
    public function __construct(
        protected readonly string $namespaceFull,
        protected readonly string $className,
        protected readonly ?string $alias = null,
        protected readonly bool $isArray = false,
    ) {
    }

    public function getNamespaceFull(): string
    {
        return $this->namespaceFull;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function isArray(): bool
    {
        return $this->isArray;
    }
}
