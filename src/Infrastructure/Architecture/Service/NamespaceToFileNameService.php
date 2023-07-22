<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service;

use App\Bundles\InfrastructureBundle\Infrastructure\Helper\Path\Directory;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ServiceDto;

class NamespaceToFileNameService
{
    public function create(string $namespace): ServiceDto
    {
        $paths = array_filter(explode('\\', $namespace), fn($value) => !is_null($value) && $value !== '');
        $file = (new Directory(Directory::getDocumentRoot(true), ...$paths))->deleteLastSlash()->getResult() . '.php';

        return new ServiceDto($file, $namespace);
    }
}
