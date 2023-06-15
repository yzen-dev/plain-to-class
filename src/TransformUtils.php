<?php

declare(strict_types=1);

namespace ClassTransformer;

use function ucfirst;
use function ucwords;
use function lcfirst;
use function is_string;
use function strtolower;
use function preg_match;
use function str_replace;
use function preg_replace;

/**
 *
 */
final class TransformUtils
{
    /** @var array<string> */
    private static array $camelCache = [];

    /** @var array<string> */
    private static array $snakeCache = [];

    /** @var array<string> */
    private static array $mutationSetterCache = [];

    /**
     * @param string $key
     *
     * @return string
     */
    public static function attributeToSnakeCase(string $key): string
    {
        if (isset(self::$snakeCache[$key])) {
            return self::$snakeCache[$key];
        }
        $str = preg_replace('/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/', '_', $key) ?? '';
        return self::$snakeCache[$key] = strtolower($str);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public static function attributeToCamelCase(string $key): string
    {
        if (isset(self::$camelCache[$key])) {
            return self::$camelCache[$key];
        }
        $str = lcfirst(str_replace('_', '', ucwords($key, '_')));
        return self::$camelCache[$key] = $str;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public static function mutationSetterToCamelCase(string $key): string
    {
        if (isset(self::$mutationSetterCache[$key])) {
            return self::$mutationSetterCache[$key];
        }
        $str = 'set' . ucfirst(self::attributeToCamelCase($key)) . 'Attribute';
        return self::$mutationSetterCache[$key] = $str;
    }

    /**
     * @param string $phpDoc
     *
     * @return string|null
     */
    public static function getClassFromPhpDoc(string $phpDoc): ?string
    {
        preg_match('/array<([a-zA-Z\d\\\]+)>/', $phpDoc, $arrayType);
        return $arrayType[1] ?? null;
    }
}
