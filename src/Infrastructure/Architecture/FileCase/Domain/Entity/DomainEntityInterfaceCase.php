<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Entity;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Entity\DefaultDomainEntityInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Entity\Getters\DomainEntityGettersInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Entity\Setters\DomainEntitySettersInterfaceCase;

class DomainEntityInterfaceCase extends DefaultDomainEntityInterfaceCase implements ArchitectureFileCaseInterface
{

    public function __construct(
        AttributesCreatorFacade $attributesCreator,
        protected readonly DomainEntityGettersInterfaceCase $gettersInterfaceCase,
        protected readonly DomainEntitySettersInterfaceCase $settersInterfaceCase
    ) {
        parent::__construct($attributesCreator);
    }

    public function create(ArchitectureFileCaseDto $caseParameters): ArchitectureFileDto
    {
        return parent::create($caseParameters)
            ->setExtendsCollection(
                $this->createCollection()
                    ->add($this->gettersInterfaceCase->createUse($caseParameters))
                    ->add($this->settersInterfaceCase->createUse($caseParameters))
            );
    }
}
