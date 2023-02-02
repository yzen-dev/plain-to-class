<?php

namespace ClassTransformer\Validators;

use ClassTransformer\Exceptions\ClassNotFoundException;

/**
 * Class ClassExistsValidator
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
class ClassExistsValidator
{
    /**
     * @param class-string $className
     *
     * @throws ClassNotFoundException
     */
    public function __construct($className)
    {
        if (!class_exists($className)) {
            throw new ClassNotFoundException("Class $className not found. Please check the class path you specified.");
        }
    }
}
