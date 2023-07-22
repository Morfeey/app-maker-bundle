<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Cases\DefaultCases;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCaseDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocDto as Doc;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\DocTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Entity\DomainEntityInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Cases\DocCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Service\DocMethodInterfaceCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\TypeCreatorService;
use ReflectionMethod;

abstract class MethodOnlyEntityInterfaceParameter implements DocCaseInterface
{

    use PrototypeHelper;
    public function __construct(
        protected readonly DomainEntityInterfaceCase $domainEntityInterfaceCase,
        protected readonly AttributesCreatorFacade $attributesCreatorFacade,
        protected readonly DocMethodInterfaceCreatorService $docMethodInterfaceCreatorService,
        protected readonly TypeCreatorService $typeCreatorService
    ) {
    }

    public function create(FileCaseDto $case, ReflectionMethod $method): Doc
    {
        $parameters = [];
        foreach ($method->getParameters() as $parameter) {
            if ($parameter->getName() === 'entity') {
                $entityUse = $this->domainEntityInterfaceCase->createUse($case);
                $parameters[] = $this->attributesCreatorFacade->createParameter(
                    $parameter->getName(),
                    $this->typeCreatorService->create(false, true, $entityUse->getClassName()),
                    null,
                    null,
                    false,
                    $entityUse
                );
            }
        }

        return $this->attributesCreatorFacade->createDoc(
            DocTypeEnum::METHOD,
            $this->attributesCreatorFacade->createMethod(
                $method->getName(),
                '',
                $this->createCollection()->setItems($parameters)
            )
        );
    }
}
