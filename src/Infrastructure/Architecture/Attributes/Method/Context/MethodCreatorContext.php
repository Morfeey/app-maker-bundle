<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Context;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Cases\MethodCaseInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Method\Dto\MethodParametersDto;

class MethodCreatorContext
{
    public function __construct(
        protected readonly iterable $cases
    ) {
    }

    public function create(MethodParametersDto $parameters): ?MethodCaseInterface
    {
        /** @var MethodCaseInterface $case */
        foreach ($this->cases as $case) {
            if (!$case->isCanCreate($parameters)) {
                continue;
            }

            return $case;
        }

        return null;
    }
}
