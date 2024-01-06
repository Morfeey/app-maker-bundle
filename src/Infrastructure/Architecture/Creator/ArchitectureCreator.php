<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Creator;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\FileUseCollectorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Context\ArchitectureFileCaseContext;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\DefaultArchitectureCreatorByDoctrineEntityClassInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\CaseService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\FileCreatorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\NamespaceToFileNameService;
use ReflectionObject;

readonly class ArchitectureCreator implements DefaultArchitectureCreatorByDoctrineEntityClassInterface
{
    public function __construct(
        private NamespaceToFileNameService $namespaceToFileNameService,
        private ArchitectureFileCaseContext $caseContext,
        private CaseService $caseService,
        private FileCreatorService $fileCreatorService,
        private FileUseCollectorService $useCollectorService
    ) {
    }

    public function create(string $entityClassName, bool $isDisableOverride): void
    {
        foreach ($this->caseContext->create() as $case) {
            $architectureFileCaseDto = $this->createFileCase($entityClassName, $case);
            $architectureFileDto = $this->collectUses($case->create($architectureFileCaseDto));

            $this->fileCreatorService->create($architectureFileDto, $architectureFileCaseDto, $isDisableOverride);
        }
    }

    public function createFileCase(string $entityClassName, ArchitectureFileCaseInterface $case): ArchitectureFileCaseDto
    {
        $entityDto = $this->namespaceToFileNameService->create($entityClassName);
        $classNameEntity = $entityDto->getNamespace();
        $doctrineEntity = new $classNameEntity;

        return
            (new ArchitectureFileCaseDto())
                ->setEntity($doctrineEntity)
                ->setReflectionEntity(new ReflectionObject($doctrineEntity))
                ->setServiceDto($entityDto)
                ->setCaseDto($this->caseService->create($case, $entityDto));
    }

    private function collectUses(ArchitectureFileDto $fileDto): ArchitectureFileDto
    {
        return $fileDto->setUseNamespaceCollection($this->useCollectorService->collect($fileDto));
    }
}
