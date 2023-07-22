<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto;

class ServiceDto
{
    public function __construct(
        protected readonly string $fileName,
        protected readonly string $namespace
    ) {
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }
}
