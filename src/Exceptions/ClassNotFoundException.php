<?php

namespace ClassTransformer\Exceptions;

use Exception;

/**
 * @psalm-api
 */
class ClassNotFoundException extends Exception
{
    /**
     * @param string $class
     */
    public function __construct(string $class)
    {
        parent::__construct("Class $class not found. Please check the class path you specified.");
    }
}
