<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\FileCaseDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ServiceDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\ArchitectureFileCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper\NamespaceHelper;

class CaseService
{
    use NamespaceHelper;
    public const TEMPLATE_FILE_NAME = 'template.twig';

    public function create(ArchitectureFileCaseInterface $case, ServiceDto $serviceDto): FileCaseDto
    {
        $caseClass = get_class($case);
        $namespace = str_replace($this->getBundleNamespace($caseClass), $this->getBundleNamespace($serviceDto->getNamespace()), $caseClass);
        $namespace = str_replace('\\Infrastructure\\Architecture\\FileCase', '', $namespace);

        $namespaceExplode = explode('\\', $namespace);
        $className = $namespaceExplode[array_key_last($namespaceExplode)];
        $className = str_replace('Case', '', $className);
        $className = $this->getEntityNameWithoutEntity($serviceDto->getNamespace()) . $className;
        $namespaceExplode[array_key_last($namespaceExplode)] = $className;
        $namespace = implode('\\', $namespaceExplode);

        $fileName = $this->getPhpFileNameByNamespace($namespace);
        $template = $this->getTwigTemplateFileByNamespace($caseClass);

        return new FileCaseDto($template, $namespace, $fileName);
    }
}
