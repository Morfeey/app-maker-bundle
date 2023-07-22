<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Cases;

use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Dto\MethodParametersDto as MethodParameters;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\MethodDto as Method;

interface MethodCaseInterface
{
    public function isCanCreate(MethodParameters $parameters): bool;

    /** @return CollectionInterface<Method>|Method[] */
    public function create(MethodParameters $parameters): CollectionInterface;
}
