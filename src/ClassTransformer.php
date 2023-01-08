<?php

namespace ClassTransformer;

use ReflectionException;
use ClassTransformer\Exceptions\ClassNotFoundException;

/**
 * Class ClassTransformer
 *
 * @package ClassTransformer
 */
final class ClassTransformer
{
    /**
     * Class-transformer function to transform our object into a typed object
     *
     * @template T
     *
     * @param class-string<T>|array<class-string<T>> $className
     * @param array<mixed>|object|null $args
     *
     * @return null|T|array<T>
     * @throws ClassNotFoundException|ReflectionException
     */
    public static function transform(string|array $className, ...$args)
    {
        if (is_string($className)) {
            return self::dataConverting($className, ...$args);
        }

        if (sizeof(func_get_args()) === 1) {
            throw new \RuntimeException('Input parameter error. Named arguments are not supported for an anonymous array of classes');
        }

        if (empty($args) || !is_array($args[0])) {
            return null;
        }

        if (sizeof($className) === 1) {
            return self::anonymousArrayConverting($className[0], $args[0]);
        }

        return self::extractArrayConverting($className, $args[0]);
    }

    /**
     * @template T
     *
     * @param class-string<T> $className
     * @param array<mixed> $args
     *
     * @return array<T>
     * @throws ClassNotFoundException|ReflectionException
     */
    private static function anonymousArrayConverting(string $className, array $args): array
    {
        $result = [];
        foreach ($args as $item) {
            /** @var T $item */
            $result [] = self::dataConverting($className, $item);
        }
        return $result;
    }

    /**
     * @template T
     *
     * @param array<class-string<T>> $className
     * @param array<mixed> $args
     *
     * @return array<T>
     * @throws ClassNotFoundException|ReflectionException
     */
    private static function extractArrayConverting(array $className, $args): array
    {
        $result = [];
        foreach ($className as $key => $class) {
            $result [] = self::dataConverting($class, $args[$key]);
        }
        return $result;
    }

    /**
     * @template T
     *
     * @param class-string<T> $className
     * @param array<mixed>|object|null $args
     *
     * @return T
     * @throws ClassNotFoundException|ReflectionException
     */
    private static function dataConverting(string $className, ...$args)
    {
        if (!class_exists($className)) {
            throw new ClassNotFoundException("Class $className not found. Please check the class path you specified.");
        }

        if (method_exists($className, 'transform')) {
            return $className::transform(...$args);
        }

        if (empty($args)) {
            return new $className();
        }
        return PropertyTransformer::init($className, ...$args)->transform();
    }
}
