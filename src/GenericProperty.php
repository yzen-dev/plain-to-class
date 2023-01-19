<?php

namespace ClassTransformer;

use ReflectionType;
use ReflectionProperty;
use ReflectionAttribute;
use ReflectionNamedType;
use ReflectionUnionType;
use ClassTransformer\Attributes\NotTransform;

use function sizeof;
use function in_array;
use function enum_exists;
use function array_intersect;

/**
 * Class GenericProperty
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
final class GenericProperty
{
    /** @var ReflectionProperty */
    public ReflectionProperty $property;

    /** @var null|ReflectionType|ReflectionUnionType|ReflectionNamedType */
    readonly public ?ReflectionType $type;

    /** @var array|string[] */
    readonly public array $types;

    /** @var class-string|string $propertyClass */
    readonly public string $name;

    /** @var string */
    readonly public string $class;

    /** @var bool */
    readonly public bool $isScalar;

    /** @var array<array<array<string>>> */
    private static $attributeTypesCache = [];

    /** @var array<array<array<ReflectionAttribute>>> */
    private static $attributesCache = [];


    /**
     * @param ReflectionProperty $property
     */
    public function __construct(ReflectionProperty $property)
    {
        $this->property = $property;
        $this->class = $property->class;
        $this->type = $this->property->getType();
        $this->name = $this->property->name;
        $this->types = $this->initTypes();
        $this->isScalar = sizeof(array_intersect($this->types, ['int', 'float', 'double', 'string', 'bool', 'mixed'])) > 0;
    }

    /**
     * @return bool
     */
    public function isEnum(): bool
    {
        if ($this->type instanceof ReflectionNamedType) {
            return enum_exists($this->type->getName());
        }
        return false;
    }

    /**
     * @return array<string>
     */
    private function initTypes(): array
    {
        if (isset(static::$attributeTypesCache[$this->class][$this->name])) {
            return static::$attributeTypesCache[$this->class][$this->name];
        }

        if ($this->type === null) {
            return [];
        }

        $types = [];
        if ($this->type instanceof ReflectionUnionType) {
            foreach ($this->type->getTypes() as $type) {
                $types [] = $type->getName();
            }
        }

        if ($this->type instanceof ReflectionNamedType) {
            $types = [$this->type->getName()];
        }

        if ($this->type->allowsNull()) {
            $types [] = 'null';
        }

        return static::$attributeTypesCache[$this->class][$this->name] = $types;
    }

    /**
     * Finds whether a variable is an array
     *
     * @return bool
     */
    public function isArray(): bool
    {
        return in_array('array', $this->types, true);
    }

    /**
     */
    public function getDocComment(): bool|string
    {
        return $this->property->getDocComment();
    }

    /**
     * @return bool
     */
    public function notTransform(): bool
    {
        return $this->getAttribute(NotTransform::class) !== null;
    }

    /**
     * @param class-string<T>|null $name
     *
     * @template T
     * @return null|T
     */
    public function getAttribute(?string $name = null): ?ReflectionAttribute
    {
        if (isset(static::$attributesCache[$this->class][$this->name][$name])) {
            return static::$attributesCache[$this->class][$this->name][$name];
        }

        $attr = $this->property->getAttributes($name);
        if (!empty($attr)) {
            return static::$attributesCache[$this->class][$this->name][$name] = $attr[0];
        }
        return null;
    }
}
