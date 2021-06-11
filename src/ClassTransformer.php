<?php

namespace ClassTransformer;

use ClassTransformer\Exceptions\ClassNotFoundException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionUnionType;

/**
 * Class ClassTransformer
 * @package ClassTransformer
 */
class ClassTransformer
{
    /**
     * Class-transformer function to transform our object into a typed object
     * @template T
     *
     * @param class-string<T> $className
     * @param array<mixed>|object|null $args
     *
     * @return T
     * @throws ClassNotFoundException|ReflectionException
     */
    public static function transform(string $className, $args)
    {
        try {
            $refInstance = new ReflectionClass($className);
        } catch (\ReflectionException $e) {
            throw new ClassNotFoundException("Class $className not found. Please check the class path you specified.");
        }

        if ($args === null) {
            return new $className();
        }

        if (method_exists($className, 'transform')) {
            return $className::transform($args);
        }

        if (is_object($args)) {
            $args = (array)$args;
        }

        $instance = new $className();
        foreach ($refInstance->getProperties() as $item) {
            if (array_key_exists($item->name, $args)) {
                $propertyClass = $refInstance->getProperty($item->name);
                $propertyClassType = $propertyClass->getType();

                $propertyClassTypeName = [];
                if ($propertyClassType instanceof ReflectionUnionType) {
                    $propertyClassTypeName = array_map(
                        static function ($item) {
                            return $item->getName();
                        },
                        $propertyClassType->getTypes()
                    );
                } elseif ($propertyClassType instanceof ReflectionNamedType) {
                    $propertyClassTypeName = [$propertyClassType->getName()];
                }

                ## if scalar type
                if (count(array_intersect_key($propertyClassTypeName, ['int', 'float', 'string', 'bool'])) > 0) {
                    $instance->{$item->name} = $args[$item->name];
                    continue;
                }

                if (array_key_exists('array', $propertyClassTypeName)) {
                    $docType = self::getClassFromPhpDoc($propertyClass->getDocComment());
                    if ($docType) {
                        foreach ($args[$item->name] as $el) {
                            /** @phpstan-ignore-next-line */
                            $instance->{$item->name}[] = self::transform($docType, $el);
                        }
                        continue;
                    }
                }
                if (!empty($propertyClassTypeName) && $propertyClassType instanceof ReflectionNamedType) {
                    /** @phpstan-ignore-next-line */
                    $instance->{$item->name} = self::transform($propertyClassTypeName, $args[$item->name]);
                    continue;
                }
                $instance->{$item->name} = $args[$item->name];
            }
        }
        return $instance;
    }

    /**
     * @param string|false $phpDoc
     * @return string|null
     */
    private static function getClassFromPhpDoc($phpDoc): ?string
    {
        if ($phpDoc) {
            preg_match('/array<([a-zA-Z\d\\\]+)>/m', $phpDoc, $docType);
            return $docType[1] ?? null;
        }
        return null;
    }
}
