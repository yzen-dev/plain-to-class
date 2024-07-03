<?php

namespace ClassTransformer;

use ReflectionException;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\Exceptions\InvalidArgumentException;
use ClassTransformer\Exceptions\InstantiableClassException;
use function method_exists;

/**
 * Class ClassRepository
 *
 * @psalm-api
 * @template T
 * @author yzen.dev <yzen.dev@gmail.com>
 */
final class Hydrator
{
    /** @var InstanceFactory */
    private InstanceFactory $instanceFactory;

    /**
     */
    public function __construct()
    {
        $this->instanceFactory = new InstanceFactory();
    }

    /**
     * Create instance T class
     *
     * @param class-string<T> $class
     * @param iterable<mixed>|object|string ...$args
     *
     * @return null|T
     * @throws ClassNotFoundException|InstantiableClassException|ReflectionException|InvalidArgumentException
     */
    public function create(string $class, ...$args): mixed
    {
        $instance = $this->instanceFactory->getInstance($class, ...$args);

        if (method_exists($instance, 'afterTransform')) {
            $instance->afterTransform();
        }

        return $instance;
    }

    /**
     * @param class-string<T> $class
     * @param array<iterable<mixed>> $args
     *
     * @return null|array<null>|array<T>
     * @throws ClassNotFoundException|InstantiableClassException|ReflectionException|InvalidArgumentException
     */
    public function createCollection(string $class, array $args): ?array
    {
        $result = [];
        foreach ($args as $item) {
            $result [] = $this->create($class, $item);
        }
        return $result;
    }

    /**
     * @param array<class-string<T>> $classes
     * @param array<iterable<mixed>> $args
     *
     * @return null|array<null>|array<T>
     * @throws ClassNotFoundException|InstantiableClassException|ReflectionException|InvalidArgumentException
     */
    public function createMultiple(array $classes, array $args): ?array
    {
        $result = [];
        foreach ($classes as $key => $class) {
            $result [] = $this->create($class, $args[$key]);
        }
        return $result;
    }
}
