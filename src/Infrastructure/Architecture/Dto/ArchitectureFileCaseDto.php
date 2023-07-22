<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\FileCaseDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ServiceDto;
use App\Bundles\InfrastructureBundle\Infrastructure\Doctrine\Entity\CustomEntityInterface;
use ReflectionObject;

class ArchitectureFileCaseDto
{
    protected ServiceDto $serviceDto;
    protected CustomEntityInterface $entity;
    protected ReflectionObject $reflectionEntity;
    protected FileCaseDto $caseDto;

    public function getServiceDto(): ServiceDto
    {
        return $this->serviceDto;
    }

    public function getEntity(): CustomEntityInterface
    {
        return $this->entity;
    }

    public function getReflectionEntity(): ReflectionObject
    {
        return $this->reflectionEntity;
    }

    public function getCaseDto(): FileCaseDto
    {
        return $this->caseDto;
    }

    public function setCaseDto(FileCaseDto $caseDto): static
    {
        $this->caseDto = $caseDto;
        return $this;
    }

    public function setServiceDto(ServiceDto $serviceDto): static
    {
        $this->serviceDto = $serviceDto;
        return $this;
    }

    public function setEntity(CustomEntityInterface $entity): static
    {
        $this->entity = $entity;
        return $this;
    }

    public function setReflectionEntity(ReflectionObject $reflectionEntity): static
    {
        $this->reflectionEntity = $reflectionEntity;
        return $this;
    }
}
