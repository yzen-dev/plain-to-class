<?php

namespace ClassTransformer\Contracts;

/**
 * @psalm-api
 * @template-covariant TClass
 */
interface ClassTransformable
{
    /**
     * @param array<mixed> $args
     *
     * @return TClass
     */
    public function transform(...$args): mixed;
}
