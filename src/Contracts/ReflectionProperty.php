<?php

namespace ClassTransformer\Contracts;

interface ReflectionProperty
{
    public function isScalar(): bool;

    public function isTransformable(): bool;

    public function getName(): string;

    public function getTypeName(): string;

    public function getAttribute(string $name);

    /**
     * Finds whether a variable is an enum
     *
     * @return bool
     */
    public function isEnum(): bool;

    /**
     * Finds whether a variable is an array
     *
     * @return bool
     */
    public function isArray(): bool;

    /**
     * @return bool
     */
    public function notTransform(): bool;

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasSetMutator(): bool;
}
