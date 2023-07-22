<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Cases;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Cases\DocCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCaseDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Cases\DefaultCases\MethodOnlyFilterInterfaceParameter;
use ReflectionMethod;

class DocMethodFindIdListByFilterCase extends MethodOnlyFilterInterfaceParameter implements DocCaseInterface
{

    public function isCanBeProcessed(FileCaseDto $case, ReflectionMethod $method): bool
    {
        return $method->getName() === 'findIdListByFilter';
    }
}
