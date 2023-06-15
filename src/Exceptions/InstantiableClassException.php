<?php

declare(strict_types=1);

namespace ClassTransformer\Exceptions;

use LogicException;

/**
 * @psalm-api
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
