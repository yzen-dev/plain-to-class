<?php

namespace ClassTransformer\Contracts;

/**
 * @template T
 */
interface ClassTransformable
{
    /**
     * @param array<mixed> $args
     *
     * @return T
     */
    public function transform(...$args): mixed;
}
