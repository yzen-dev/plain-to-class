<?php

namespace ClassTransformer\Reflection;

use ReflectionProperty;

/**
 * Class GenericProperty
 *
 * @psalm-api
 */
final class CacheReflectionProperty implements \ClassTransformer\Contracts\ReflectionProperty
{

    public string $class;
    public string $name;
    public string $type;
    public array $types;
    public bool $isScalar;
    public bool $hasSetMutator;
    public bool $isArray;
    public bool $isEnum;
    public bool $notTransform;
    public bool $isTransformable;
    public ?string $typeName;
    public string $docComment;
    public array $attributes;

    /**
     */
    public function __construct()
    {
    }

    /**
     * @return bool
     */
    public function hasSetMutator(): bool
    {
        return $this->hasSetMutator;
    }

    /**
     * @return bool
     */
    public function isEnum(): bool
    {
        return $this->isEnum;
    }

    /**
     * @return bool
     */
    public function isArray(): bool
    {
        return $this->isArray;
    }

    /**
     * @return bool
     */
    public function notTransform(): bool
    {
        return $this->notTransform;
    }

    /**
     * @return bool
     */
    public function isScalar(): bool
    {
        return $this->isScalar;
    }

    /**
     * @return bool
     */
    public function isTransformable(): bool
    {
        return $this->isTransformable;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return null|class-string
     */
    public function getTypeName(): ?string
    {
        return $this->typeName;
    }


    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function getAttribute(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * @param string|null $name
     *
     * @return mixed|null
     */
    public function getAttributeArguments(?string $name = null): ?array
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * @return bool|string
     */
    public function getDocComment(): bool|string
    {
        return $this->docComment ?? false;
    }

}
