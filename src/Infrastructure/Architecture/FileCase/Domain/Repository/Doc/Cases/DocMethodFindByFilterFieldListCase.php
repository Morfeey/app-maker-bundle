<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Cases;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Cases\DocCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCaseDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocDto as Doc;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Filter\Entity\FieldListInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Filter\FilterInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Cases\DefaultCases\MethodOnlyFilterInterfaceParameter;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Service\DocMethodInterfaceCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Service\DocMethodParameterInterfaceCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\TypeCreatorService;
use ReflectionMethod;

class DocMethodFindByFilterFieldListCase extends MethodOnlyFilterInterfaceParameter implements DocCaseInterface
{

    public function __construct(
        DocMethodInterfaceCreatorService $docMethodInterfaceCreatorService,
        DocMethodParameterInterfaceCreatorService $docMethodParameterInterfaceCreatorService,
        TypeCreatorService $typeCreatorService,
        FilterInterfaceCase $filterInterfaceCase,
        protected readonly FieldListInterfaceCase $fieldListInterfaceCase
    ) {
        parent::__construct($docMethodInterfaceCreatorService, $docMethodParameterInterfaceCreatorService, $typeCreatorService, $filterInterfaceCase);
    }

    public function isCanBeProcessed(FileCaseDto $case, ReflectionMethod $method): bool
    {
        return $method->getName() === 'findByFilterFieldList';
    }

    public function create(FileCaseDto $case, ReflectionMethod $method): Doc
    {
        $parameters = [];
        $filterParameter = $this->createFilterParameter($method, $case);
        if ($filterParameter) {
            $parameters[] = $filterParameter;
        }

        foreach ($method->getParameters() as $parameter) {
            if ($parameter->getName() === 'fieldList') {
                $fieldListUse = $this->fieldListInterfaceCase->createUse($case);
                $parameters[] = $this->docMethodParameterInterfaceCreatorService->create(
                    $parameter,
                    $this->typeCreatorService->create(
                        true,
                        true,
                        $fieldListUse->getClassName(),
                        $fieldListUse
                    )
                );
            }
        }

        return $this->docMethodInterfaceCreatorService->createByMethod(
            $method,
            ...$parameters
        );
    }
}
