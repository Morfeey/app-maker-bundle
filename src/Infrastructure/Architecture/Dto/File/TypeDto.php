<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;

class TypeDto
{
    public function __construct(
        protected readonly bool $isVariadic,
        protected readonly bool $isObject,
        protected readonly ?string $stringType,
        protected readonly ?UseNamespaceDto $namespace,
    ) {
    }

    public function isVariadic(): bool
    {
        return $this->isVariadic;
    }

    public function isObject(): bool
    {
        return $this->isObject;
    }

    public function getStringType(): ?string
    {
        return $this->stringType;
    }

    public function getNamespace(): ?UseNamespaceDto
    {
        return $this->namespace;
    }
}
