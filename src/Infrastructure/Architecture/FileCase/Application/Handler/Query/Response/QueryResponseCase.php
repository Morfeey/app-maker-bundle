<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Handler\Query\Response;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Constructor\Context\ConstructorCaseContext;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Constructor\Enum\ConstructorTypeEnum as ConstructorType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Enum\MethodCaseEnum as MethodCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto as UseNamespace;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Query\Response\QueryResponseInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\DefaultFileCase;

class QueryResponseCase extends DefaultFileCase implements ArchitectureFileCaseInterface
{

    public function __construct(
        private readonly AttributesCreatorFacade $attributesCreatorFacade,
        private readonly QueryResponseInterfaceCase $contractEntityInterfaceCase,
        private readonly ConstructorCaseContext $constructorCaseContext
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        return
            $this->createDefault($caseParameters)
                ->setConstructor($this->constructorCaseContext->create(ConstructorType::DTO, $caseParameters))
                ->setMethodCollection(
                    $this->attributesCreatorFacade->createMethods(
                        $this->attributesCreatorFacade->createMethodParameters($caseParameters, MethodCase::GETTER)
                    )
                )
                ->setImplementsCollection(
                    $this->createCollection()
                        ->add($this->contractEntityInterfaceCase->createUse($caseParameters))
                );
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespace
    {
        return $this->createDefaultUse($this->attributesCreatorFacade, $fileCase, $isArray);
    }
}
