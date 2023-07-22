<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\NamespaceHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto;

trait CreatorDefaultArchitectureFileDtoHelper
{
    use NamespaceHelper;
    protected ?ArchitectureFileDto $architectureFileDto;

    public function createDefault(ArchitectureFileCaseDto $architectureFileCaseDto): ArchitectureFileDto
    {
        return
            clone $this->createArchitectureFilePrototype()
                ->setClassName($this->getClassNameByNamespace($architectureFileCaseDto->getCaseDto()->getNamespace()))
                ->setNamespace($this->getNamespaceWithoutClassName($architectureFileCaseDto->getCaseDto()->getNamespace()));
    }

    private function createArchitectureFilePrototype(): ArchitectureFileDto
    {
        $this->architectureFileDto = $this->architectureFileDto ?? new ArchitectureFileDto();

        return clone $this->architectureFileDto;
    }
}
