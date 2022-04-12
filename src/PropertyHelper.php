<?php

namespace ClassTransformer;

/**
 * Class PropertyHelper
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
class PropertyHelper
{
    /**
     * @param string|false $phpDoc
     *
     * @return string|null
     */
    public static function getClassFromPhpDoc($phpDoc): ?string
    {
        if ($phpDoc) {
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
            return count(array_intersect($type, ['int', 'float', 'string', 'bool', 'mixed'])) > 0;
        }
        return in_array($type, ['int', 'float', 'string', 'bool', 'mixed']);
    }
}
