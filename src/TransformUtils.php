<?php

namespace ClassTransformer;

use function ucwords;
use function lcfirst;
use function strtolower;
use function str_replace;
use function preg_replace;

/**
 *
 */
final class TransformUtils
{
    /**
     * @param string $string
     *
     * @return string
     */
    public static function strToSnakeCase(string $string): string
    {
        $str = preg_replace('/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/', '_', $string) ?? '';
        return strtolower($str);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function strToCamelCase(string $string): string
    {
        return lcfirst(str_replace('_', '', ucwords($string, '_')));
    }

    /**
     * @param string|bool $phpDoc
     *
     * @return string|null
     */
    public static function getClassFromPhpDoc($phpDoc): ?string
    {
        if (is_string($phpDoc)) {
            preg_match('/array<([a-zA-Z\d\\\]+)>/m', $phpDoc, $arrayType);
            return $arrayType[1] ?? null;
        }
        return null;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public static function propertyIsScalar(string $type): bool
    {
        return in_array($type, ['int', 'float', 'string', 'bool', 'mixed']);
    }
}
