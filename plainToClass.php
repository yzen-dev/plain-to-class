<?php

if (!function_exists('plainToClass')) {
    /**
     * Class-transformer function to transform our object into a typed object
     * @template T
     *
     * @param T $class
     * @param   $args
     *
     * @return T
     */
    function plainToClass($class, $args)
    {
        $instance = new $class;
        if ($args !== null) {
            if (method_exists($class,'plainToClass')){
                return $class::plainToClass($args);
            }
            foreach ($args as $key => $val) {
                if (property_exists($instance, $key)) {
                    $instance->{$key} = $val;
                }
            }
        }
        return $instance;
    }
}
