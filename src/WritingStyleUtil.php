<?php

namespace ClassTransformer;

class WritingStyleUtil
{
    /**
     * @param $string
     * @return string
     */
    public static function strToSnakeCase($string)
    {
        return strtolower(preg_replace('/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/', '_', $string));
    }

    /**
     * @param $string
     * @return string
     */
    public static function strToCamelCase($string)
    {
        $str = str_replace('_', '', ucwords($string, '_'));
        return lcfirst($str);
    }
}