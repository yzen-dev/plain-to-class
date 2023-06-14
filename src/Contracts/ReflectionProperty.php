<?php

namespace ClassTransformer\Contracts;

use ClassTransformer\Reflection\Types\PropertyType;

/**
 * @psalm-api
 */
interface ReflectionProperty
{

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return PropertyType
     */
    public function getType(): PropertyType;

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
