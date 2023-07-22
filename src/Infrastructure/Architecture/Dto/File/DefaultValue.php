<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File;

class DefaultValue
{
    public function __construct(
        protected readonly mixed $value,
        protected readonly ?string $valueContent = null
    ) {
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getValueContent(): ?string
    {
        return $this->valueContent;
    }
}
