<?php

namespace ClassTransformer;

use _PHPStan_76800bfb5\Nette\Utils\Paginator;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionUnionType;
use ClassTransformer\Exceptions\ClassNotFoundException;

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
    public static function transform(string $className, ...$args)
    {
        $isPHP8Format = count(func_get_args()) === 1;

        try {
            $refInstance = new ReflectionClass($className);
        } catch (\ReflectionException $e) {
            throw new ClassNotFoundException("Class $className not found. Please check the class path you specified.");
        }

        if (empty($args) === null) {
            return new $className();
        }

        if (method_exists($className, 'transform')) {
            if ($isPHP8Format) {
                return $className::transform(...$args);
            }
            return $className::transform($args[0]);
        }
        // Если обычный массив, то надо распаковать
        // Иначе он валидный

        if ($isPHP8Format) {
            $inArgs = $args;
        } else {
            $inArgs = $args[0];
        }

        if (is_object($inArgs)) {
            $inArgs = (array)$inArgs;
        }
        $instance = new $className();
        foreach ($refInstance->getProperties() as $item) {
            if (!array_key_exists($item->name, $inArgs)) {
                continue;
            }

            $property = $refInstance->getProperty($item->name);
            $propertyType = $property->getType();

            $propertyClassTypeName = [];
            if ($propertyType instanceof ReflectionUnionType) {
                $propertyClassTypeName = array_map(
                    static function ($item) {
                        return $item->getName();
                    },
                    $propertyType->getTypes()
                );
            } elseif ($propertyType instanceof ReflectionNamedType) {
                $propertyClassTypeName = [$propertyType->getName()];
            }

            if (count(array_intersect($propertyClassTypeName, ['int', 'float', 'string', 'bool'])) > 0) {
                $instance->{$item->name} = $inArgs[$item->name];
                continue;
            }

            if (in_array('array', $propertyClassTypeName, true)) {
                $docType = self::getClassFromPhpDoc($property->getDocComment());
                if (!empty($docType)) {
                    foreach ($inArgs[$item->name] as $el) {
                        /** @phpstan-ignore-next-line */
                        $instance->{$item->name}[] = self::transform($docType, $el);
                    }
                    continue;
                }
            }

            if ($propertyType instanceof ReflectionNamedType) {
                /** @phpstan-ignore-next-line */
                $instance->{$item->name} = self::transform($propertyType->getName(), $inArgs[$item->name]);
                continue;
            }
            $instance->{$item->name} = $inArgs[$item->name];

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
