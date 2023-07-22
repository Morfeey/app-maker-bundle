<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Query\Response;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto as ArchitectureFile;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ClassTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Entity\Getters\DomainEntityGettersInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\CreatorDefaultArchitectureFileDtoHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;

class QueryResponseInterfaceCase implements ArchitectureFileCaseInterface
{

    use PrototypeHelper, CreatorDefaultArchitectureFileDtoHelper;
    public function __construct(
        protected readonly AttributesCreatorFacade $attributesCreatorFacade,
        protected readonly DomainEntityGettersInterfaceCase $domainEntityGettersInterfaceCase
    ) {
    }

    public function create(FileCase $caseParameters): ArchitectureFile
    {
        return $this->createDefault($caseParameters)
            ->setClassType(ClassTypeEnum::INTERFACE_)
            ->setExtendsCollection(
                $this->createCollection()
                    ->add($this->domainEntityGettersInterfaceCase->createUse($caseParameters))
            );
    }

    public function createUse(FileCase $fileCase, bool $isArray = false): UseNamespaceDto
    {
        $fileCase = $this->attributesCreatorFacade->createCaseDtoByCase($fileCase, $this);
        $file = $this->createDefault($fileCase);

        return
            $this->attributesCreatorFacade->createUse(
                $this->mergeNamesapce($file->getNamespace(), $file->getClassName()),
                $file->getClassName(),
                null,
                $isArray
            );
    }
}
