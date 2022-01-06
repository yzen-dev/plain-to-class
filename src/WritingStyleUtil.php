<?php

namespace ClassTransformer;

/**
 *
 */
class WritingStyleUtil
{
    /**
     * @param string $string
     * @return string
     */
    public static function strToSnakeCase(string $string): string
    {
        $str = preg_replace('/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/', '_', $string) ?? '';
        return strtolower($str);
    }

    /**
     * @param string $string
     * @return string
     */
    public static function strToCamelCase(string $string): string
    {
        return lcfirst(str_replace('_', '', ucwords($string, '_')));
    }
}
