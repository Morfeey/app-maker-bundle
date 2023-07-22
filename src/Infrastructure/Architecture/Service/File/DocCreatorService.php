<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocDescriptionDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocTypeInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\DocTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;

class DocCreatorService
{
    use PrototypeHelper;
    public function create(DocTypeEnum $type, DocTypeInterface $entity, UseNamespaceDto ...$useNamespaceDtos): DocDto
    {
        $useCollection = $this->createCollection();
        foreach ($useNamespaceDtos as $useNamespaceDto) {
            $useCollection->add($useNamespaceDto);
        }

        return new DocDto($useCollection, $type, $entity);
    }

    public function createDescription(string $description): DocDescriptionDto
    {
        return new DocDescriptionDto($description);
    }
}
