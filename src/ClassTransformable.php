<?php

namespace ClassTransformer;

use ReflectionException;

/**
 * @template T
 */
interface ClassTransformable
{
    /**
     * @param array<mixed> $args
     *
     * @return T
     * @throws ReflectionException
     */
    public function transform(...$args): mixed;
}
