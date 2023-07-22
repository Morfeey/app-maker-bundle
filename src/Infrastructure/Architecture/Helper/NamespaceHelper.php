<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper;

use App\Bundles\InfrastructureBundle\Infrastructure\Helper\Path\Directory;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\Path\StringValue;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\CaseService;

trait NamespaceHelper
{
    public function getPhpFileNameByNamespace(string $namespace): string
    {
        $namespace = ltrim($namespace, '\\');
        $namespace = rtrim($namespace, '\\');
        $string =
            (new StringValue($namespace))
                ->replace('App\\Bundles', NamespaceHelper . phpDirectory::getDocumentRoot(true) . DIRECTORY_SEPARATOR . 'bundles')
                ->replace('\\', DIRECTORY_SEPARATOR)
                ->getResult();
        $string = rtrim($string, DIRECTORY_SEPARATOR);

        return $string . '.php';
    }

    public function mergeNamesapce(string ...$namespaces): string
    {
        return implode('\\', $namespaces);
    }

    public function getTwigTemplateFileByNamespace(string $namespace): string
    {
        $string = $this->getPhpFileNameByNamespace($namespace);
        $explode = explode(DIRECTORY_SEPARATOR, $string);
        $explode[array_key_last($explode)] = CaseService::TEMPLATE_FILE_NAME;
        $string = implode(DIRECTORY_SEPARATOR, $explode);
        $string = str_replace('FileCase', 'Template', $string);

        return (new Directory($string))->deleteLastSlash()->getResult();
    }

    public function getBundleNamespace(string $namespace): string
    {
        $doBundleItems = [];
        $namespace = trim($namespace, '\\');
        $explode = explode('\\', $namespace);
        foreach ($explode as $item) {
            $string = new StringValue($item);
            if ($string->isContains('Bundle') && !$string->isContains('Bundles')) {
                $doBundleItems[] = $item;
                break;
            }

            $doBundleItems[] = $item;
        }

        return implode('\\', $doBundleItems);
    }

    public function getEntityName(string $namespace): string
    {
        $namespace = rtrim($namespace, '\\');
        $explode = explode('\\', $namespace);

        return $explode[array_key_last($explode)];
    }

    public function getEntityNameWithoutEntity(string $namespace): string
    {
        return str_replace('Entity', '', $this->getEntityName($namespace));
    }

    public function getClassNameByNamespace(string $namespace): string
    {
        $explode = explode('\\', $namespace);

        return count($explode) ? $explode[array_key_last($explode)] : $namespace;
    }

    public function getNamespaceWithoutClassName(string $namespace): string
    {
        $explode = explode('\\', $namespace);
        $lastKey = array_key_last($explode);
        $namespaceWithoutClassName = [];
        foreach ($explode as $key => $string) {
            if ($key === $lastKey) {
                continue;
            }

            $namespaceWithoutClassName[] = $string;
        }

        return implode('\\', $namespaceWithoutClassName);
    }
}
