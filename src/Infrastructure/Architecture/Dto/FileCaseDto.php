<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto;

class FileCaseDto
{
    public function __construct(
        protected readonly string $template,
        protected readonly string $namespace,
        protected string $fileName
    ) {
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;
        return $this;
    }
}
