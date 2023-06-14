<?php

namespace ClassTransformer\Exceptions;

use LogicException;

/**
 * @infection-ignore-all
 */
class InstantiableClassException extends LogicException
{
    /**
     * @param string $class
     */
    public function __construct(string $class)
    {
        parent::__construct('Class ' . $class . ' is not instantiable.');
    }
}
