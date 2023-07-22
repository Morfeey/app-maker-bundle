<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper;

use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\Collection;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\FileUseCollectorService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\TypeDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;

trait MultiTypeHelper
{

    public function createMultiType(TypeDto $type): TypeDto
    {
        $stringType = $type->getStringType();
        if ($type->isVariadic() && !in_array($type->getStringType(), FileUseCollectorService::SIMPLE_TYPES)) {
            $stringType = 'CollectionInterface';
        } elseif ($type->isVariadic()) {
            $stringType = 'array';
        }

        return new TypeDto(
            $type->isVariadic(),
            $type->isObject(),
            $stringType,
            $type->getNamespace());
    }

    public function createUseByMultiType(TypeDto $type): CollectionInterface
    {
        $usesCollection = $this->createCollection();
        if ($type->getStringType() === 'CollectionInterface') {
            $interface = new UseNamespaceDto(CollectionInterface::class, 'CollectionInterface');
            $collection = new UseNamespaceDto(Collection::class, 'Collection');
            $usesCollection->add($interface)->add($collection);
        }

        return $usesCollection;
    }
}
