<?php

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Helper;

use App\Bundles\InfrastructureBundle\Infrastructure\Helper\Path\StringValue;

trait StringValuePrototypeTrait
{
    protected StringValue $stringValuePrototype;

    public function createPrototypeStringValue(): StringValue
    {
        $this->stringValuePrototype = $this->stringValuePrototype ?? new StringValue();

        return clone $this->stringValuePrototype;
    }
}
