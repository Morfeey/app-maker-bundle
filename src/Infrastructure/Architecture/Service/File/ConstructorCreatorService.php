<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ConstructorDto as Constructor;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\FieldDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ParameterDto as Dependency;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File\UseCreatorService;

class ConstructorCreatorService
{
    public function __construct(
        protected UseCreatorService $useCreatorService
    ) {
    }

    use PrototypeHelper;
    public function create(?string $content = null, Dependency ...$dependencies): Constructor
    {
        $dependencyCollection = $this->createCollection();
        foreach ($dependencies as $dependency) {
            $dependencyCollection->add($dependency);
        }

        return new Constructor($dependencyCollection, $content);
    }

    public function createContentByFields(FieldDto ...$fields): string
    {
        return
            implode(
                '\n',
                array_map(
                    static function (FieldDto $field) {
                        if ($field->getType()->getStringType() === 'CollectionInterface'
                            || $field->getType()->getStringType() === 'Collection'
                        ) {
                            return "\$this->{$field->getName()} = new Collection(\${$field->getName()});";
                        }

                        return "\$this->{$field->getName()} = \${$field->getName()};";
                    },
                    $fields
                )
            );
    }
}
