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
     * @return bool
     */
    public function transformable(): bool;

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
     * @return null|array<string>
     */
    public function getAttributeArguments(string $name): ?array;

    /**
     * @return string
     */
    public function getDocComment(): string;

    /**
     * Finds whether a variable is an enum
     *
     * @return bool
     */
    public function isEnum(): bool;

    /**
     * @return bool
     */
    public function hasSetMutator(): bool;

    /**
     * @return bool
     */
    public function notTransform(): bool;

    /**
     * @return array<string>
     */
    public function getAliases(): array;
}
