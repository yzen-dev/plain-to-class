<?php

namespace ClassTransformer;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionUnionType;
use ClassTransformer\Attributes\WritingStyle;
use ClassTransformer\Attributes\ConvertArray;
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
        // arguments transferred as named arguments (for php8)
        $isNamedArguments = count(func_get_args()) === 1;

        if (!class_exists($className)) {
            throw new ClassNotFoundException("Class $className not found. Please check the class path you specified.");
        }

        $refInstance = new ReflectionClass($className);

        if (empty($args) === null) {
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

        $instance = new $className();
        foreach ($refInstance->getProperties() as $item) {
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

            $property = $refInstance->getProperty($item->name);
            $propertyType = $property->getType();
            $propertyClassTypeName = self::getPropertyTypes($propertyType);

            if (count(array_intersect($propertyClassTypeName, ['int', 'float', 'string', 'bool'])) > 0) {
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
                if (!empty($arrayType)) {
                    foreach ($value as $el) {
                        /** @phpstan-ignore-next-line */
                        $instance->{$item->name}[] = self::transform($arrayType, $el);
                    }
                    continue;
                }
            }

            if ($propertyType instanceof ReflectionNamedType) {
                /** @phpstan-ignore-next-line */
                $instance->{$item->name} = self::transform($propertyType->getName(), $value);
                continue;
            }
            $instance->{$item->name} = $value;
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
            preg_match('/array<([a-zA-Z\d\\\]+)>/m', $phpDoc, $arrayType);
            return $arrayType[1] ?? null;
        }
        return null;
    }

    private static function getPropertyTypes($propertyType)
    {
        if ($propertyType instanceof ReflectionUnionType) {
            return array_map(
                static function ($item) {
                    return $item->getName();
                },
                $propertyType->getTypes()
            );
        } elseif ($propertyType instanceof ReflectionNamedType) {
            return [$propertyType->getName()];
        }
        return [];
    }
}
