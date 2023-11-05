<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Facade\NonArchitect\Service;

use App\Bundles\InfrastructureBundle\Infrastructure\Exception\InvalidArgumentException\InvalidArgumentException;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\Collection;
use App\Bundles\InfrastructureBundle\Infrastructure\Helper\ArrayCollection\CollectionInterface;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Attributes\Service\AttributesCreatorFacade;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\ArchitectureFileCaseDto as FileCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\DocDto as Doc;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\MethodDto as Method;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\ParameterDto;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Dto\File\UseNamespaceDto as UseNamespace;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\DocTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\MethodTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Enum\ModificationTypeEnum;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\ArchitectureCqrsRequestInterface as Request;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\HandlerInterfaceCaseInterface as Handler;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\Handler\Query\Response\QueryResponseInterfaceCase;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Application\Contract\NonArchitect\Enum\RequestCqrsType;
use App\Bundles\AppMakerBundle\Infrastructure\Architecture\FileCase\Domain\Entity\DomainEntityInterfaceCase;

readonly class FacadeService
{
    public function __construct(
        private AttributesCreatorFacade $attributes,
        private QueryResponseInterfaceCase $queryResponseInterfaceCase,
        private DomainEntityInterfaceCase $domainEntityInterfaceCase
    ) {
    }

    public function createMethodByHandler(Handler $handler, FileCase $caseParameters, bool $isVirtual, bool $isParentDependencies = false): Method
    {
        $docParameters = $this->attributes->createCollection();
        $notParentParameters = $this->createMethodParametersByQuery($handler->getRequest(), $caseParameters);
        foreach ($notParentParameters as $parameter) {
            $docParameters->add(
                $this->attributes->createDoc(
                    DocTypeEnum::PARAM,
                    $parameter,
                    ...$parameter->getUseCollection()->toArray()
                )
            );
        }

        $method = $this->attributes->createMethod(
            $this->createMethodNameByQuery($handler->getRequest(), $caseParameters),
            $this->getInvokeMethodByHandler($handler, $caseParameters)->getTypeHint(),
            $this->createMethodParametersByQuery($handler->getRequest(), $caseParameters, $isParentDependencies),
            MethodTypeEnum::NON_STATIC,
            ModificationTypeEnum::PUBLIC_,
            $isVirtual,
            $isVirtual ? null : $this->createMethodContent($handler->getRequest(), $caseParameters)
        );

        $returns = $this->createReturnsByMethod($method, $caseParameters);
        $docReturns = $this->attributes->createCollection();
        $docReturns->add(
            $this->attributes->createDoc(
                DocTypeEnum::RETURN,
                $method,
                ...$returns
            )
        );

        return $method->setDocCollection($docParameters->mergeWithoutReplacement($docReturns));
    }

    public function createMethodContent(Request $request, FileCase $caseParameters): string
    {
        $busName = "{$request->getType()->getValue()}Bus";
        $parametersToStringList = [];
        foreach ($request->create($caseParameters)->getConstructor()->getDependencies() as $dependency) {
            $parametersToStringList[] = ($dependency->getType()->isVariadic() ? '...' : '') . '$' . $dependency->getName();
        }

        if ($request->getType() === RequestCqrsType::QUERY) {
            return "return \$this->{$busName}->execute(new {$request->createUse($caseParameters)->getClassName()}(" . implode(', ', $parametersToStringList) . '));';
        }

        return implode(PHP_EOL, [
            "\$this->{$busName}->execute(new {$request->createUse($caseParameters)->getClassName()}(" . implode(', ', $parametersToStringList) . '));',
            '',
            '        return $this;'
        ]);
    }

    public function createDocByMethod(Method $method, UseNamespace ...$returns): Doc
    {
        return $this->attributes->createDoc(DocTypeEnum::METHOD, $method, ...$returns);
    }

    public function getInvokeMethodByHandler(Handler $handler, FileCase $caseParameters): Method
    {
        $methods = $handler->create($caseParameters)->getMethodCollection();
        if (!$methods->count()) {
            throw new InvalidArgumentException(get_class($handler) . ' methods list is empty');
        }

        foreach ($methods as $method) {
            if ($method->getName() === '__invoke') {
                return $method;
            }
        }

        throw new InvalidArgumentException(get_class($handler) . ' __invoke method not found');
    }

    public function createMethodNameByQuery(Request $query, FileCase $caseParameters): string
    {
        return
            $this->attributes->createPrototypeStringValue()
                ->setString($query->createUse($caseParameters)->getClassName())
                ->replace('Query')
                ->replace('Command')
                ->replace($this->attributes->getEntityNameWithoutEntity($caseParameters->getServiceDto()->getNamespace()))
                ->toCamelCase()
                ->firstCharLow()
                ->getResult();
    }

    /**
     * @return CollectionInterface<int, ParameterDto>|ParameterDto[]
     */
    public function createMethodParametersByQuery(Request $query, FileCase $caseParameters, bool $isParentDependencies = false): CollectionInterface
    {
        $methodParameters = $this->attributes->createCollection();
        $dependencies =
            $isParentDependencies
                ? $query->createParentDependencies($caseParameters)
                : $query->createDependencies($caseParameters);
        foreach ($dependencies as $dependency) {
            $uses = (array) $dependency->getUseCollection()?->toArray();
            $methodParameters->add(
                $this->attributes->createParameter(
                    $dependency->getName(),
                    $dependency->getType(),
                    $dependency->getValue(),
                    null,
                    false,
                    ...$uses
                )
            );
        }

        return $methodParameters;
    }

    /**
     * @return UseNamespace[]
     */
    public function createReturnsByMethod(Method $method, FileCase $caseParameters): array
    {
        $returns = [];
        $collectionInterfaceClassName = $this->attributes->getClassNameByNamespace(CollectionInterface::class);
        $collectionClassName = $this->attributes->getClassNameByNamespace(Collection::class);
        if ($method->getTypeHint() === $collectionInterfaceClassName
            || $method->getTypeHint() === $collectionClassName
        ) {
            $returns[] = $this->queryResponseInterfaceCase->createUse($caseParameters, true);
            $returns[] = $this->attributes->createUse(CollectionInterface::class);
        }

        $typeHintStringValue = $this->attributes->createPrototypeStringValue()->setString($method->getTypeHint());
        if ($typeHintStringValue->startsWith('?')) {
            $returns[] = $this->attributes->createUse('null', 'null');
        }

        $entityUse = $this->domainEntityInterfaceCase->createUse($caseParameters);
        $queryResponseUse = $this->queryResponseInterfaceCase->createUse($caseParameters);
        if ($typeHintStringValue->isContains($entityUse->getClassName()) || $typeHintStringValue->isContains($queryResponseUse->getClassName())) {
            $returns[] = $queryResponseUse;
        }

        if (empty($returns)) {
            $returns[] = $this->attributes->createUse($method->getTypeHint());
        }

        return $returns;
    }
}
