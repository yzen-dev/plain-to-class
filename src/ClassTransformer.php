<?php

namespace ClassTransformer;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use ClassTransformer\Attributes\WritingStyle;
use ClassTransformer\Exceptions\ClassNotFoundException;

/**
 * Class ClassTransformer
 *
 * @package ClassTransformer
 */
class ClassTransformer
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
    public static function transform($className, ...$args)
    {
        if (is_string($className)) {
            return self::dataConverting($className, ...$args);
        }

        if (empty($args) || !is_array($args[0])) {
            return null;
        }
        if (count($className) === 1) {
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
    private static function anonymousArrayConverting(string $className, $args)
    {
        $result = [];
        foreach ($args as $key => $item) {
            if (!is_array($item)) {
                $result [] = self::dataConverting($className, [$key => $item]);
            } else {
                $result [] = self::dataConverting($className, $item);
            }
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

        $refInstance = new ReflectionClass($className);

        if (empty($args)) {
            return new $className();
        }

        // if exist custom transform method
        if (method_exists($className, 'transform')) {
            return $className::transform($args[0]);
        }

        // if dynamic arguments, named ones lie immediately in the root, if they were passed as an array, then they need to be unpacked
        $inArgs = $args[0];

        if (is_object($inArgs)) {
            $inArgs = (array)$inArgs;
        }
        $inArgs ??= [];

        $instance = new $className();
        foreach ($refInstance->getProperties() as $item) {
            $property = $refInstance->getProperty($item->name);
            $propertyType = $property->getType();
            $propertyClassTypeName = self::getPropertyTypes($propertyType);

            if (array_key_exists($item->name, $inArgs)) {
                $value = $inArgs[$item->name];
            } else {
                $writingStyles = self::getWritingStyleFromPhpDoc($property->getDocComment());
                if (empty($writingStyles)) {
                    continue;
                }

                if ((in_array(WritingStyle::STYLE_SNAKE_CASE, $writingStyles) || in_array(WritingStyle::STYLE_ALL, $writingStyles)) && array_key_exists(WritingStyleUtil::strToSnakeCase($item->name), $inArgs)) {
                    $value = $inArgs[WritingStyleUtil::strToSnakeCase($item->name)];
                }
                if ((in_array(WritingStyle::STYLE_CAMEL_CASE, $writingStyles) || in_array(WritingStyle::STYLE_ALL, $writingStyles)) && array_key_exists(WritingStyleUtil::strToCamelCase($item->name), $inArgs)) {
                    $value = $inArgs[WritingStyleUtil::strToCamelCase($item->name)];
                }

                if (!isset($value)) {
                    continue;
                }
            }

            if (count(array_intersect($propertyClassTypeName, ['int', 'float', 'string', 'bool'])) > 0) {
                $instance->{$item->name} = $value;
                continue;
            }

            if (in_array('array', $propertyClassTypeName, true)) {
                $arrayType = self::getClassFromPhpDoc($property->getDocComment());
                if (!empty($arrayType)) {
                    foreach ($value as $el) {
                        /** @phpstan-ignore-next-line */
                        $instance->{$item->name}[] = self::dataConverting($arrayType, $el);
                    }
                    continue;
                }

                $instance->{$item->name} = $value;
                continue;
            }

            if ($propertyType instanceof ReflectionNamedType) {
                /** @phpstan-ignore-next-line */
                $instance->{$item->name} = self::dataConverting($propertyType->getName(), $value);
                continue;
            }
            $instance->{$item->name} = $value;
        }
        return $instance;
    }

    /**
     * @param string|false $phpDoc
     *
     * @return string|null
     */
    private static function getClassFromPhpDoc($phpDoc): ?string
    {
        if ($phpDoc) {
            preg_match('/array<([a-zA-Z\d\\\]+)>/m', $phpDoc, $arrayType);
            return $arrayType[1] ?? null;
        }
        return null;
    }

    /**
     * @param string|false $phpDoc
     *
     * @return null|array<string>
     */
    private static function getWritingStyleFromPhpDoc($phpDoc): ?array
    {
        if ($phpDoc) {
            preg_match('/@writingStyle<(.*?)>/m', $phpDoc, $arrayType);
            if (isset($arrayType[1])) {
                $styles = explode('|', $arrayType[1]);
                $result = [];
                foreach ($styles as $style) {
                    if (!empty($style)) {
                        $result[] = constant('ClassTransformer\Attributes\\' . $style);
                    }
                }
                return $result;
            }
        }
        return null;
    }

    /**
     * @param ReflectionType|null $propertyType
     *
     * @return array<string>
     */
    private static function getPropertyTypes(?ReflectionType $propertyType): array
    {
        if ($propertyType instanceof ReflectionNamedType) {
            return [$propertyType->getName()];
        }
        return [];
    }
}
