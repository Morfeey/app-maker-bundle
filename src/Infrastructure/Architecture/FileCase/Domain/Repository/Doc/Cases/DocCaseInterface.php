<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Cases;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCaseDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocDto as Doc;
use ReflectionMethod;

interface DocCaseInterface
{
    public function isCanBeProcessed(FileCaseDto $case, ReflectionMethod $method): bool;
    public function create(FileCaseDto $case, ReflectionMethod $method): Doc;
}
