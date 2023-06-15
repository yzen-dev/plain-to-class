<?php

namespace ClassTransformer\Contracts;

use ClassTransformer\Reflection\Types\PropertyType;

/**
 * @psalm-api
 */
abstract class ReflectionProperty
{
    /** @var class-string|string $propertyClass */
    public string $name;

    /** @var class-string */
    public string $class;

    /** @var PropertyType */
    public PropertyType $type;

    /**
     * @param string $name
     *
     * @return mixed
     */
    abstract public function getAttribute(string $name): mixed;

    /**
     * @param string $name
     *
     * @return null|array<string>
     */
    abstract public function getAttributeArguments(string $name): ?array;

    /**
     * @return string
     */
    abstract public function getDocComment(): string;

    /**
     * @return bool
     */
    abstract public function hasSetMutator(): bool;

    /**
     * @return bool
     */
    abstract public function notTransform(): bool;

    /**
     * @return array<string>
     */
    abstract public function getAliases(): array;
}
