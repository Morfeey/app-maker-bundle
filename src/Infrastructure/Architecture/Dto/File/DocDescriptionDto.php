<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File;

class DocDescriptionDto
{
    public function __construct(
        protected string $description
    ) {
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
