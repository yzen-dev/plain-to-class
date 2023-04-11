<?php

namespace ClassTransformer\Contracts;

/**
 * @psalm-api
 */
interface ReflectionProperty
{
    /**
     * @return bool
     */
    public function isScalar(): bool;

    /**
     * @return false|class-string
     */
    public function transformable(): false|string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getAttribute(string $name);

    /**
     * @param string $name
     *
     * @return ?array
     */
    public function getAttributeArguments(string $name): ?array;

    /**
     * @return bool|string
     */
    public function getDocComment(): bool|string;

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
    public function hasSetMutator(): bool;
}
