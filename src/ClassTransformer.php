<?php

declare(strict_types=1);

namespace ClassTransformer;

use ClassTransformer\Reflection\CacheReflectionClass;
use ClassTransformer\Validators\ClassExistsValidator;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\Reflection\RuntimeReflectionClass;

/**
 * Class ClassTransformer
 *
 * @psalm-api
 */
final class ClassTransformer
{
    /**
     * Class-transformer function to transform our object into a typed object
     *
     * @template T of object
     *
     * @param class-string<T> $className
     * @param iterable<mixed>|object ...$args
     *
     * @return null|T
     * @throws ClassNotFoundException
     */
    public static function transform(string $className, ...$args): mixed
    {
        new ClassExistsValidator($className);

        if (method_exists($className, 'transform')) {
            $instance = new $className();
            $instance->transform(...$args);
        } else {
            if (ClassTransformerConfig::$cache) {
                $reflection = new CacheReflectionClass($className);
            } else {
                $reflection = new RuntimeReflectionClass($className);
            }
            $generic = new GenericInstance($reflection, new ArgumentsResource(...$args));
            /** @var T $instance */
            $instance = $generic->transform();
        }

        if (method_exists($instance, 'afterTransform')) {
            $instance->afterTransform();
        }

        return $instance;
    }

    /**
     *
     * @template T of object
     *
     * @param class-string<T> $className
     * @param array<iterable<mixed>> $args
     *
     * @return null|array<null>|array<T>
     * @throws ClassNotFoundException
     */
    public static function transformCollection(string $className, array $args): ?array
    {
        $result = [];
        foreach ($args as $item) {
            $result [] = self::transform($className, $item);
        }
        return $result;
    }

    /**
     *
     * @template T of object
     *
     * @param array<class-string<T>> $className
     * @param array<iterable<mixed>> $args
     *
     * @return null|array<null>|array<T>
     * @throws ClassNotFoundException
     */
    public static function transformMultiple(array $className, array $args): ?array
    {
        $result = [];
        foreach ($className as $key => $class) {
            $result [] = self::transform($class, $args[$key]);
        }
        return $result;
    }
}
