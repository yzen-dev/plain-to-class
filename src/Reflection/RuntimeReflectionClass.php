<?php

declare(strict_types=1);

namespace ClassTransformer\Reflection;

use ReflectionClass;
use ReflectionException;
use InvalidArgumentException;
use ClassTransformer\Contracts\ReflectionClassRepository;

use function array_map;

/**
 * Class RuntimeReflectionClass
 *
 * @psalm-api
 * @template T
 */
final class RuntimeReflectionClass implements ReflectionClassRepository
{
    /** @var class-string $class */
    private string $class;

    /**
     * @param class-string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * @return RuntimeReflectionProperty[]
     * @throws ReflectionException|InvalidArgumentException
     */
    public function getProperties(): array
    {
        $refInstance = new ReflectionClass($this->class);

        if (!$refInstance->isInstantiable()) {
            throw new InvalidArgumentException('Class ' . $this->class . ' is not instantiable.');
        }

        $properties = $refInstance->getProperties();

        return array_map(static fn($item) => new RuntimeReflectionProperty($item), $properties);
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }
}
