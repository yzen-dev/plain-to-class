<?php

namespace ClassTransformer;

use ReflectionException;
use ClassTransformer\Reflection\ClassSchema;
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
final class InstanceFactory
{
    /**
     * @var array<string,ClassSchema>
     */
    private static array $classRepositoryCache = [];

    /**
     * @param class-string<T> $class
     * @param iterable<mixed>|object ...$args
     *
     * @return mixed
     * @throws ClassNotFoundException|InstantiableClassException|ReflectionException|InvalidArgumentException
     */
    public function getInstance(string $class, ...$args): mixed
    {
        if (method_exists($class, 'transform')) {
            $reflection = new \ReflectionClass($class);
            $instance = $reflection->newInstanceWithoutConstructor();
            $instance->transform(...$args);

            return $instance;
        }

        return (new InstanceBuilder(
            $this->createClassSchema($class),
            new ArgumentsRepository(...$args)
        ))
            ->build();
    }

    /**
     * @param class-string<T> $class
     *
     * @return ClassSchema
     * @throws ClassNotFoundException|ClassNotFoundException|InstantiableClassException
     */
    private function createClassSchema(string $class): ClassSchema
    {
        if (isset(self::$classRepositoryCache[$class])) {
            return self::$classRepositoryCache[$class];
        }

        return self::$classRepositoryCache[$class] = new ClassSchema($class);
    }
}
