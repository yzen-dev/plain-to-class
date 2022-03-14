<?php

namespace ClassTransformer;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use ClassTransformer\Attributes\WritingStyle;
use ClassTransformer\Attributes\ConvertArray;
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
    public static function transform(string|array $className, ...$args)
    {
        if (is_string($className)) {
            return self::dataConverting($className, ...$args);
        }

        if (count(func_get_args()) === 1) {
            throw new \RuntimeException('Input parameter error. Named arguments are not supported for an anonymous array of classes');
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
        foreach ($args as $item) {
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
        // arguments transferred as named arguments (for php8)
        $isNamedArguments = count(func_get_args()) === 1;

        if (!class_exists($className)) {
            throw new ClassNotFoundException("Class $className not found. Please check the class path you specified.");
        }

        $refInstance = new ReflectionClass($className);

        if (empty($args)) {
            return new $className();
        }

        // if exist custom transform method
        if (method_exists($className, 'transform')) {
            if ($isNamedArguments) {
                return $className::transform(...$args);
            }
            return $className::transform($args[0]);
        }

        // if dynamic arguments, named ones lie immediately in the root, if they were passed as an array, then they need to be unpacked
        $inArgs = $isNamedArguments ? $args : $args[0];

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
                $writingStyle = $item->getAttributes(WritingStyle::class);

                if (empty($writingStyle)) {
                    continue;
                }
                foreach ($writingStyle as $style) {
                    $styles = $style->getArguments();
                    if ((in_array(WritingStyle::STYLE_SNAKE_CASE, $styles) || in_array(WritingStyle::STYLE_ALL, $styles)) && array_key_exists(WritingStyleUtil::strToSnakeCase($item->name), $inArgs)) {
                        $value = $inArgs[WritingStyleUtil::strToSnakeCase($item->name)];
                        break;
                    }
                    if ((in_array(WritingStyle::STYLE_CAMEL_CASE, $styles) || in_array(WritingStyle::STYLE_ALL, $styles)) && array_key_exists(WritingStyleUtil::strToCamelCase($item->name), $inArgs)) {
                        $value = $inArgs[WritingStyleUtil::strToCamelCase($item->name)];
                        break;
                    }
                }

                if (!isset($value)) {
                    continue;
                }
            }

            if (self::propertyIsScalar($propertyClassTypeName)) {
                $instance->{$item->name} = $value;
                continue;
            }

            if (in_array('array', $propertyClassTypeName, true)) {
                // ConvertArray
                $arrayTypeAttr = $item->getAttributes(ConvertArray::class);
                if (!empty($arrayTypeAttr)) {
                    $arrayType = $arrayTypeAttr[0]->getArguments()[0];
                } else {
                    $arrayType = self::getClassFromPhpDoc($property->getDocComment());
                }

                if (!empty($arrayType) && !empty($value) && !self::propertyIsScalar($arrayType)) {
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
     * @param array<string>|string $type
     *
     * @return bool
     */
    private static function propertyIsScalar(array|string $type): bool
    {
        if (is_array($type)) {
            return count(array_intersect($type, ['int', 'float', 'string', 'bool', 'mixed'])) > 0;
        }
        return in_array($type, ['int', 'float', 'string', 'bool', 'mixed']);
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
     * @param ReflectionType|null $propertyType
     *
     * @return array<string>
     */
    private static function getPropertyTypes(?ReflectionType $propertyType): array
    {
        if ($propertyType instanceof ReflectionUnionType) {
            return array_map(
                static function ($item) {
                    return $item->getName();
                },
                $propertyType->getTypes()
            );
        }
        if ($propertyType instanceof ReflectionNamedType) {
            return [$propertyType->getName()];
        }
        return [];
    }
}
