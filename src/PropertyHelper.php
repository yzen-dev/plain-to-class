<?php

namespace ClassTransformer;

use function in_array;
use function is_array;
use function sizeof;
use function array_intersect;
/**
 * Class PropertyHelper
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
final class PropertyHelper
{
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
     * @param array<string>|string $type
     *
     * @return bool
     */
    public static function propertyIsScalar(array|string $type): bool
    {
        if (is_array($type)) {
            return sizeof(array_intersect($type, ['int', 'float', 'string', 'bool', 'mixed'])) > 0;
        }
        return in_array($type, ['int', 'float', 'string', 'bool', 'mixed']);
    }
}
