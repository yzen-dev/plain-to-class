<?php

declare(strict_types=1);

namespace ClassTransformer;

use ClassTransformer\Exceptions\ClassNotFoundException;
use RuntimeException;

/**
 * Class ClassTransformer
 *
 * @psalm-api
 * @deprecated
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
     * @throws ClassNotFoundException|RuntimeException
     */
    public static function transform(string $className, ...$args): mixed
    {
        return (new Hydrator())
            ->create($className, ...$args);
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
