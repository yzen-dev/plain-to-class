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
     * @return string
     */
    public function getType(): ?string;

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getAttribute(string $name): mixed;

    /**
     * @param string $name
     *
     * @return ?array
     */
    public function getAttributeArguments(string $name): ?array;

    /**
     * @return bool|string
     */
    public function getDocComment(): string;

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

    /**
     * @return bool
     */
    public function notTransform(): bool;
}