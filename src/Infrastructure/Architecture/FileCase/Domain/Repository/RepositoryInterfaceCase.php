<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository;

use App\Bundles\InfrastructureBundle\Domain\Repository\DomainRepositoryInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Creator\ArchitectureCreator;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Entity\DomainEntityInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Repository\Doc\Context\DocCaseContext;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\CreatorDefaultArchitectureFileDtoHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\NamespaceHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\UseCreatorService;
use ReflectionClass;
use ReflectionMethod;

class RepositoryInterfaceCase implements ArchitectureFileCaseInterface
{

    use NamespaceHelper, CreatorDefaultArchitectureFileDtoHelper, PrototypeHelper;
    public function __construct(
        protected readonly AttributesCreatorFacade $attributesCreatorFacade,
        protected readonly UseCreatorService $useCreatorService,
        protected readonly DomainEntityInterfaceCase $domainEntityInterfaceCase,
        protected readonly ArchitectureCreator $architectureCreator,
        protected readonly DocCaseContext $docCaseContext
    ) {
    }

    public function create(ArchitectureFileCaseDto $caseParameters): ArchitectureFileDto
    {
        $reflectionRepository = new ReflectionClass(DomainRepositoryInterface::class);
        $docs = [];
        foreach ($reflectionRepository->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $doc = $this->docCaseContext->create($caseParameters, $method);
            if (!$doc) {
                continue;
            }

            $docs[] = $doc;
        }

        return $this
            ->createDefault($caseParameters)
            ->setClassType(ClassTypeEnum::INTERFACE_)
            ->setDescription($this->attributesCreatorFacade->createDocDescription('Domain repository interface'))
            ->setDocCollection($this->createCollection()->setItems($docs))
            ->setExtendsCollection(
                $this->createCollection()
                    ->add($this->attributesCreatorFacade->createUse(DomainRepositoryInterface::class))
            );
    }

    public function createUse(ArchitectureFileCaseDto $fileCase, bool $isArray = false): UseNamespaceDto
    {
        $fileCase = $this->attributesCreatorFacade->createCaseDtoByCase($fileCase, $this);
        return $this->attributesCreatorFacade->createUse(
            $fileCase->getCaseDto()->getNamespace(),
            null,
            null,
            $isArray
        );
    }
}
