<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Cases;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Cases\DocCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCaseDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocDto as Doc;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\DocTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Entity\DomainEntityInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;
use ReflectionMethod;

readonly class DocMethodCreateCase implements DocCaseInterface
{

    use PrototypeHelper;
    public function __construct(
        private AttributesCreatorFacade $attributesCreatorFacade,
        private DomainEntityInterfaceCase $domainEntityInterfaceCase
    ) {
    }

    public function isCanBeProcessed(FileCaseDto $case, ReflectionMethod $method): bool
    {
        return $method->getName() === 'create';
    }

    public function create(FileCaseDto $case, ReflectionMethod $method): Doc
    {
        return $this->attributesCreatorFacade->createDoc(
            DocTypeEnum::METHOD,
            $this->attributesCreatorFacade->createMethod(
                $method->getName(),
                '',
                $this->createCollection()
            ),
            $this->domainEntityInterfaceCase->createUse($case)
        );
    }
}
