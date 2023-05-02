<?php

namespace ClassTransformer;

use ClassTransformer\Reflection\CacheReflectionClass;
use ClassTransformer\Validators\ClassExistsValidator;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\Reflection\RuntimeReflectionClass;

/**
 * Class ClassTransformer
 *
 * @psalm-api
 * @psalm-immutable
 */
final class ClassTransformer
{
    /**
     * Class-transformer function to transform our object into a typed object
     *
     * @template TClass
     *
     * @param class-string<TClass> $className
     * @param iterable<mixed>|object ...$args
     *
     * @return null|TClass
     * @throws ClassNotFoundException
     */
    public static function transform(string $className, ...$args)
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
            /** @var TClass $instance */
            $instance = $generic->transform();
        }

        if (method_exists($instance, 'afterTransform')) {
            $instance->afterTransform();
        }

        return $instance;
    }

    /**
     * @template TClass
     *
     * @param class-string<TClass> $className
     * @param array<iterable<mixed>> $args
     *
     * @return null|array<null>|array<TClass>
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
     * @template TClass
     *
     * @param array<class-string<TClass>> $className
     * @param array<iterable<mixed>> $args
     *
     * @return null|array<null>|array<TClass>
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
