<?php

namespace ClassTransformer;

use ClassTransformer\Contracts\ClassTransformable;
use ClassTransformer\Exceptions\ClassNotFoundException;

/**
 * Class ClassTransformer
 *
 * @template T of ClassTransformable
 * @package ClassTransformer
 */
final class ClassTransformer
{
    /**
     * Class-transformer function to transform our object into a typed object
     *
     * @param class-string<T> $className
     * @param iterable<mixed> ...$args
     *
     * @return null|T
     * @throws ClassNotFoundException
     */
    public static function transform(string $className, ...$args)
    {
        return (new TransformBuilder($className, ...$args))
            ->build();
    }

    /**
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
