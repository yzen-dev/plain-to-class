<?php

namespace ClassTransformer\Reflection;

use ReflectionType;
use ReflectionProperty;
use ReflectionNamedType;
use ReflectionUnionType;
use ReflectionAttribute;
use ClassTransformer\TransformUtils;
use ClassTransformer\Attributes\NotTransform;

use function method_exists;
use function array_intersect;
use function in_array;
use function sizeof;

/**
 * Class GenericProperty
 */
final class RuntimeReflectionProperty implements \ClassTransformer\Contracts\ReflectionProperty
{
    /** @var ReflectionProperty */
    public ReflectionProperty $property;

    /** @var ReflectionType|ReflectionNamedType|ReflectionUnionType|null */
    public ReflectionType|ReflectionNamedType|ReflectionUnionType|null $type;

    /** @var array|string[] */
    public array $types;

    /** @var class-string|string $propertyClass */
    public string $name;

    /** @var class-string */
    public string $class;

    /** @var bool */
    public bool $isScalar;

    /** @var array<class-string,<array<string>>> */
    private static array $attributeTypesCache = [];

    /** @var array<class-string,array<string, array<ReflectionAttribute>>> */
    private static array $attributesCache = [];

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
        if (!function_exists('enum_exists')) {
            return false;
        }
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
        if (isset(self::$attributeTypesCache[$this->class][$this->name])) {
            return self::$attributeTypesCache[$this->class][$this->name];
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

        return self::$attributeTypesCache[$this->class][$this->name] = $types;
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
     * @return null|ReflectionAttribute
     */
    public function getAttribute(?string $name = null): ?ReflectionAttribute
    {
        if (isset(self::$attributesCache[$this->class][$this->name][$name])) {
            return self::$attributesCache[$this->class][$this->name][$name];
        }

        $attr = $this->property->getAttributes($name);
        if (!empty($attr)) {
            return self::$attributesCache[$this->class][$this->name][$name] = $attr[0];
        }
        return null;
    }

    /**
     * @param string|null $name
     *
     * @return array|null
     */
    public function getAttributeArguments(?string $name = null): ?array
    {
        return $this->getAttribute($name)?->getArguments();
    }

    /**
     * @return bool
     */
    public function hasSetMutator(): bool
    {
        return method_exists($this->class, TransformUtils::mutationSetterToCamelCase($this->name));
    }

    /**
     * @return bool
     */
    public function isScalar(): bool
    {
        return $this->isScalar;
    }

    /**
     * @return false|class-string
     */
    public function transformable(): false|string
    {
        return $this->type instanceof ReflectionNamedType ? $this->type->getName() : false;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
