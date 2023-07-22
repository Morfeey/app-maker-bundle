<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service;

use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\Collection;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\MethodDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;

class FileUseCollectorService
{
    public const SIMPLE_TYPES = ['int', 'string', 'bool', 'float', 'null', 'mixed', 'array', 'static', 'self'];
    public function collect(ArchitectureFileDto $architectureFile): CollectionInterface
    {
        $useCollection = new Collection();
        $useCollection->mergeWithoutReplacement(
            $architectureFile->getUseNamespaceCollection(),
            $architectureFile->getExtendsCollection(),
            $architectureFile->getImplementsCollection()
        );
        foreach ($architectureFile->getFieldCollection() as $field) {
            if ($field->getUseCollection()?->count()) {
                $useCollection->mergeWithoutReplacement($field->getUseCollection());
            }
        }

        foreach ($architectureFile->getMethodCollection() as $method) {
            if ($method->getUseCollection()->count()) {
                $useCollection->mergeWithoutReplacement($method->getUseCollection());
            }

            $useCollection = $this->collectByParameters($method->getParameters(), $useCollection);
        }

        foreach ($architectureFile->getDocCollection() as $doc) {
            $useCollection->mergeWithoutReplacement($doc->getUseCollection());
            $entity = $doc->getEntity();
            if ($entity instanceof MethodDto) {
                $useCollection->mergeWithoutReplacement($entity->getUseCollection());
                $useCollection = $this->collectByParameters($entity->getParameters(), $useCollection);
            }
        }

        if ($architectureFile->getConstructor()) {
            $useCollection = $this->collectByParameters($architectureFile->getConstructor()->getDependencies(), $useCollection);
        }

        return $this->uniqueUseCollection($useCollection);
    }

    protected function collectByParameters(CollectionInterface $parameters, CollectionInterface $useCollection): CollectionInterface
    {
        foreach ($parameters as $parameter) {
            if ($parameter->getUseCollection()->count()) {
                $useCollection->mergeWithoutReplacement($parameter->getUseCollection());
            }

            if ($parameter->getType()->getNamespace()) {
                $useCollection->add($parameter->getType()->getNamespace());
            }
        }

        return $useCollection;
    }

    protected function uniqueUseCollection(CollectionInterface $useCollection): CollectionInterface
    {
        $useList = [];
        $namespaceList = [];
        /**
         * @var UseNamespaceDto $use
         * @var UseNamespaceDto $tempUse
         */
        foreach ($useCollection as $use) {
            $useAliasCreated = null;
            foreach ($useCollection as $tempUse) {
                if ($use->getNamespaceFull() === $tempUse->getNamespaceFull() && $tempUse->getAlias()) {
                    $useAliasCreated = $tempUse;
                    break;
                }
            }


            $currentUse = $useAliasCreated ?? $use;
            if (!in_array($currentUse->getClassName(), self::SIMPLE_TYPES)
                && !in_array($currentUse->getNamespaceFull(), $namespaceList)
                && !str_starts_with($currentUse->getClassName(), '?')
            ) {
                $namespaceList[] = $currentUse->getNamespaceFull();
                $useList[] = $currentUse;
            }
        }

        return
            (new Collection($useList))
                ->sortByCallback(
                    static function (UseNamespaceDto $firstItem, UseNamespaceDto $secondItem) {
                        return $firstItem->getNamespaceFull() <=> $secondItem->getNamespaceFull();
                    }
                );
    }
}
