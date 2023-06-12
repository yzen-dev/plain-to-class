<?php

declare(strict_types=1);

namespace ClassTransformer\Reflection;

use ClassTransformer\Attributes\FieldAlias;

/**
 * Class GenericProperty
 *
 * @psalm-api
 */
final class CacheReflectionProperty implements \ClassTransformer\Contracts\ReflectionProperty
{
    /**
     */
    public function __construct(
        public string $class,
        public string $name,
        public ?string $type,
        public array $types,
        public bool $isScalar,
        public bool $hasSetMutator,
        public bool $isArray,
        public bool $isEnum,
        public bool $notTransform,
        public false|string $transformable,
        public string $docComment,
        public array $attributes,
    ) {
    }

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
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
     * @return false|class-string
     */
    public function transformable(): false|string
    {
        return $this->transformable;
    }

    /**
     * @return bool
     */
    public function isScalar(): bool
    {
        return $this->isScalar;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
        $aliases = $this->getAttributeArguments(FieldAlias::class);
        
        if (empty($aliases)) {
            return [];
        }
        
        $aliases = $aliases[0];
        if (is_string($aliases)) {
            $aliases = [$aliases];
        }
        return $aliases;
    }
}
