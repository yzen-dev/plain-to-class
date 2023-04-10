<?php

namespace ClassTransformer;

use ClassTransformer\Attributes\ConvertArray;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ReflectionType;
use ReflectionProperty;
use ReflectionAttribute;
use ReflectionNamedType;
use ReflectionUnionType;
use ClassTransformer\Attributes\NotTransform;

use function sizeof;
use function in_array;
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
    public ?ReflectionType $type;

    /** @var array|string[] */
    public array $types;

    /** @var class-string|string $propertyClass */
    public string $name;

    /** @var string */
    public string $class;

    /** @var bool */
    public bool $isScalar;

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
     * @return null|ReflectionAttribute
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

    /**
     * @param GenericProperty $property
     * @param mixed $value
     *
     * @return mixed
     * @throws ClassNotFoundException
     */
    public function castAttribute($value)
    {
        if ($this->isScalar || $this->notTransform()) {
            return $value;
        }

        if ($this->isArray()) {
            return $this->castArray($value);
        }

        if ($this->isEnum() && (is_string($value) || is_int($value))) {
            return $this->castEnum($value);
        }

        if ($this->type instanceof ReflectionNamedType) {
            $propertyClass = $this->type->getName();

            /** @phpstan-ignore-next-line */
            return (new TransformBuilder($propertyClass, $value))->build();
        }
        return $value;
    }


    /**
     * @param GenericProperty $property
     * @param array<mixed>|mixed $value
     *
     * @return array<mixed>|mixed
     * @throws ClassNotFoundException
     */
    private function castArray($value): mixed
    {
        $arrayTypeAttr = $this->getAttribute(ConvertArray::class);
        if ($arrayTypeAttr !== null) {
            $arrayType = $arrayTypeAttr->getArguments()[0];
        } else {
            $arrayType = TransformUtils::getClassFromPhpDoc($this->getDocComment());
        }

        if (empty($arrayType) || !is_array($value) || $arrayType === 'mixed') {
            return $value;
        }

        $array = [];
        if (!in_array($arrayType, ['int', 'float', 'string', 'bool', 'mixed'])) {
            foreach ($value as $el) {
                $array[] = (new TransformBuilder($arrayType, $el))->build();
            }
            return $array;
        }

        foreach ($value as $el) {
            $array[] = match ($arrayType) {
                'string' => (string)$el,
                'int' => (int)$el,
                'float' => (float)$el,
                'bool' => (bool)$el,
                default => $el
            };
        }
        return $array;
    }

    /**
     * @param GenericProperty $property
     * @param int|string $value
     *
     * @return mixed
     */
    private function castEnum(int|string $value)
    {
        /** @phpstan-ignore-next-line */
        $propertyClass = $this->type->getName();
        if (method_exists($propertyClass, 'from')) {
            /** @var \BackedEnum $propertyClass */
            return $propertyClass::from($value);
        }

        return constant($propertyClass . '::' . $value);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasSetMutator(): bool
    {
        return method_exists($this->class, TransformUtils::mutationSetterToCamelCase($this->name));
    }
}
