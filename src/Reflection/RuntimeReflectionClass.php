<?php

declare(strict_types=1);

namespace ClassTransformer\Reflection;

use ClassTransformer\Exceptions\ClassNotFoundException;
use ReflectionClass;
use ReflectionException;
use ClassTransformer\Contracts\ReflectionClassRepository;
use ClassTransformer\Exceptions\InstantiableClassException;

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
     * @throws InstantiableClassException|ClassNotFoundException
     */
    public function getProperties(): array
    {
        if (!class_exists($this->class)) {
            throw new ClassNotFoundException("Class $this->class not found. Please check the class path you specified.");
        }
        $refInstance = new ReflectionClass($this->class);

        if (!$refInstance->isInstantiable()) {
            throw new InstantiableClassException($this->class);
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
