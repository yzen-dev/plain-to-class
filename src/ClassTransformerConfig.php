<?php

namespace ClassTransformer;

use ClassTransformer\Exceptions\ClassNotFoundException;

/**
 * Class ClassTransformerConfig
 *
 * @psalm-api
 */
final class ClassTransformerConfig
{
    /** @var bool */
    public static bool $cache = false;
}
