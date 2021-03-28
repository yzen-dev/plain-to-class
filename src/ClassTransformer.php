<?php

namespace ClassTransformer;

use ClassTransformer\Exceptions\ClassNotFoundException;
use ReflectionClass;

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
     * @param mixed $args
     *
     * @return T
     * @throws ClassNotFoundException
     */
    public static function transform(string $className, $args)
    {
        if ($args === null) {
            return new $className();
        }

        if (method_exists($className, 'plainToClass')) {
            return $className::plainToClass($args);
        }

        try {
            $refInstance = new ReflectionClass($className);
        } catch (\ReflectionException $e) {
            throw new ClassNotFoundException("Class $className not found. Please check the class path you specified.");
        }

        if (is_object($args)) {
            $args = (array)$args;
        }

        $instance = new $className();
        foreach ($refInstance->getProperties() as $item) {
            if (array_key_exists($item->name, $args)) {
                $propertyClass = $refInstance->getProperty($item->name);
                $propertyClassType = $propertyClass->getType();
                $propertyClassTypeName = $propertyClassType !== null ? $propertyClassType->getName() : false;

                if ($propertyClassTypeName === 'array') {
                    $docType = self::getClassFromPhpDoc($propertyClass->getDocComment());
                    if ($docType && class_exists($docType)) {
                        foreach ($args[$item->name] as $el) {
                            $instance->{$item->name}[] = self::transform($docType, $el);
                        }
                    }
                    continue;
                }

                if ($propertyClassTypeName && class_exists($propertyClassTypeName)) {
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
