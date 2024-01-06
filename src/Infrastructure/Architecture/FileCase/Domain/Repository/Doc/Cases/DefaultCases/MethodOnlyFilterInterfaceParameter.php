<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Cases\DefaultCases;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCaseDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocDto as Doc;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ParameterDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Filter\FilterInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Cases\DocCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Service\DocMethodInterfaceCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Service\DocMethodParameterInterfaceCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\NamespaceHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\TypeCreatorService;
use ReflectionMethod;

abstract class MethodOnlyFilterInterfaceParameter implements DocCaseInterface
{
    use NamespaceHelper;
    public function __construct(
        protected readonly DocMethodInterfaceCreatorService $docMethodInterfaceCreatorService,
        protected readonly DocMethodParameterInterfaceCreatorService $docMethodParameterInterfaceCreatorService,
        protected readonly TypeCreatorService $typeCreatorService,
        protected readonly FilterInterfaceCase $filterInterfaceCase
    ) {
    }

    public function create(FileCaseDto $case, ReflectionMethod $method): Doc
    {
        return $this->docMethodInterfaceCreatorService->createByMethod($method, $this->createFilterParameter($method, $case));
    }

    protected function createFilterParameter(ReflectionMethod $method, FileCaseDto $case): ?ParameterDto
    {
        foreach ($method->getParameters() as $parameter) {
            if ($parameter->getName() !== 'filter') {
                continue;
            }

            $filterUse = $this->filterInterfaceCase->createUse($case);
            return $this->docMethodParameterInterfaceCreatorService->create(
                $parameter,
                $this->typeCreatorService->create(
                    false,
                    true,
                    $filterUse->getClassName(),
                    $filterUse
                )
            );
        }

        return null;
    }
}
