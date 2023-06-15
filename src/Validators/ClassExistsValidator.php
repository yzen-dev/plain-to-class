<?php

declare(strict_types=1);

namespace ClassTransformer\Validators;

use ClassTransformer\Exceptions\ClassNotFoundException;

use function class_exists;

/**
 * Class ClassExistsValidator
 */
class ClassExistsValidator
{
    /**
     * @param string $className
     *
     * @throws ClassNotFoundException
     */
    public function __construct(string $className)
    {
        if (!class_exists($className)) {
            throw new ClassNotFoundException("Class $className not found. Please check the class path you specified.");
        }
    }
}
