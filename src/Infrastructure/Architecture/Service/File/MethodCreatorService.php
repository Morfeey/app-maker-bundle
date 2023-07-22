<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\File;

use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Enum\MethodCaseEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\MethodDto as Method;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\MethodTypeEnum as MethodType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum as ModificationType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\PrototypeHelper;

class MethodCreatorService
{
    use PrototypeHelper;
    public function create(
        CollectionInterface $parameters,
        string $typehint,
        string $name,
        MethodType $type = MethodType::NON_STATIC,
        ModificationType $modificationType = ModificationType::PUBLIC_,
        bool $isVirtual = false,
        ?string $content = null,
        MethodCaseEnum $case = MethodCaseEnum::GETTER,
        ?UseNamespaceDto ...$usesNamespace
    ): Method {
        $useCollection = $this->createCollection();
        foreach ($usesNamespace as $useNamespaceDto) {
            if (!$useNamespaceDto) {
                continue;
            }

            $useCollection->add($useNamespaceDto);
        }

        return new Method($parameters, $typehint, $name, $type, $modificationType, $isVirtual, $content, $case, $useCollection);
    }
}
