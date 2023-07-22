<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Service\TemplateRenderService;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileDto;

class FileCreatorService
{
    public function __construct(
        protected readonly TemplateRenderService $templateRenderService
    ) {
    }

    public function create(ArchitectureFileDto $architectureFileDto, ArchitectureFileCaseDto $architectureFileCaseDto): static
    {
        $fileName = $architectureFileCaseDto->getCaseDto()->getFileName();
        return
            $this
                ->touch($fileName)
                ->put($fileName, $this->templateRenderService->render($architectureFileDto));
    }

    private function put(string $fileName, string $content): static
    {
        file_put_contents($fileName, $content);

        return $this;
    }

    private function touch(string $fileName): static
    {
        if (!is_dir(dirname($fileName))) {
            mkdir(dirname($fileName), 0777, true);
        }

        if (!file_exists($fileName)) {
            touch($fileName);
        }

        return $this;
    }
}
