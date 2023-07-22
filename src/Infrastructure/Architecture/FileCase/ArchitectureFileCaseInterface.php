<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;

interface ArchitectureFileCaseInterface
{
    public function create(FileCase $caseParameters): ArchitectureFile;
    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto;
}
