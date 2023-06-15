<?php

declare(strict_types=1);

namespace ClassTransformer\Reflection;

use ClassTransformer\Reflection\Types\PropertyType;

/**
 * Class GenericProperty
 *
 * @psalm-api
 */
final class CacheReflectionProperty extends \ClassTransformer\Contracts\ReflectionProperty
{
    /**
     */
    public function __construct(
        public string $class,
        public string $name,
        public PropertyType $type,
        public bool $hasSetMutator,
        public bool $notTransform,
        public string $docComment,
        public array $attributes,
        public array $aliases,
    ) {
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
    public function notTransform(): bool
    {
        return $this->notTransform;
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function getAttribute(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * @param string|null $name
     *
     * @return null|array<string>
     */
    public function getAttributeArguments(?string $name = null): ?array
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * @return string
     */
    public function getDocComment(): string
    {
        return $this->docComment;
    }

    /**
     * @return array<string>
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }
}
