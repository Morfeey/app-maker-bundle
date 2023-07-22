<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Context;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCaseDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocDto as Doc;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Cases\DocCaseInterface;
use ReflectionMethod;

class DocCaseContext
{
    public function __construct(
        protected readonly iterable $docCases
    ) {
    }

    public function create(FileCaseDto $case, ReflectionMethod $method): ?Doc
    {
        /** @var DocCaseInterface $docCase */
        foreach ($this->docCases as $docCase) {
            if ($docCase->isCanBeProcessed($case, $method)) {
                return $docCase->create($case, $method);
            }
        }

        return null;
    }
}
