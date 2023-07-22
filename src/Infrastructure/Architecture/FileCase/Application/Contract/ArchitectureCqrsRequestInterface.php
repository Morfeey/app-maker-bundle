<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract;

use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ParameterDto as Parameter;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\NonArchitect\Enum\RequestCqrsType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;

interface ArchitectureCqrsRequestInterface extends ArchitectureFileCaseInterface
{
    public function createGettersByParameters(Parameter ...$parameters): CollectionInterface;
    public function createDependencies(FileCase $caseParameters): CollectionInterface;
    public function createParentDependencies(FileCase $caseParameters): CollectionInterface;
    public function getType(): RequestCqrsType;
}
