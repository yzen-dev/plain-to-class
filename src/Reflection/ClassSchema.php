<?php

declare(strict_types=1);

namespace ClassTransformer\Reflection;

use ReflectionClass;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\Exceptions\InstantiableClassException;

/**
 * Class ClassSchema
 *
 * @template TClass
 * @author yzen.dev <yzen.dev@gmail.com>
 */
final class ClassSchema
{
    /** @psalm-param class-string<TClass> $class */
    public string $className;

    /**
     * @var array<string,ClassProperty[]>
     */
    private static array $propertiesCache = [];

    /**
     * @var ReflectionClass
     */
    private ReflectionClass $instance;

    /**
     * @param string $class
     *
     * @throws ClassNotFoundException|InstantiableClassException
     */
    public function __construct(string $class)
    {
        if (!class_exists($class)) {
            throw new ClassNotFoundException($class);
        }

        $this->instance = new ReflectionClass($class);

        if (!$this->instance->isInstantiable()) {
            throw new InstantiableClassException($class);
        }
        $this->className = $class;
    }

    /**
     * @return object
     * @throws \ReflectionException
     */
    public function makeInstance(): object
    {
        return $this->instance->newInstanceWithoutConstructor();
    }

    /**
     * @return ClassProperty[]
     */
    public function getProperties(): array
    {
        if (isset(self::$propertiesCache[$this->className])) {
            return self::$propertiesCache[$this->className];
        }

        $properties = $this->instance->getProperties();
        return self::$propertiesCache[$this->className] = array_map(static fn($item) => new ClassProperty($item), $properties);
    }
}
