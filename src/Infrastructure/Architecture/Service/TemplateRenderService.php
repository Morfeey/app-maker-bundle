<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TemplateRenderService
{
    protected function getTemplatePath(): string
    {
        return __DIR__ . '/../Template';
    }

    public function render(ArchitectureFileDto $architectureFileDto): string
    {
        $loader = new FilesystemLoader(__DIR__ . '/../Template');
        $env = new Environment($loader);

        return $env->render('template.twig', ['content' => $architectureFileDto]);
    }
}
