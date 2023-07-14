<?php

declare(strict_types=1);

namespace ClassTransformer;

use RuntimeException;
use ClassTransformer\Enums\TypeEnums;
use ClassTransformer\Reflection\Types\EnumType;
use ClassTransformer\Reflection\Types\ArrayType;
use ClassTransformer\Contracts\ReflectionProperty;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\Exceptions\InvalidArgumentException;

use function array_map;
use function is_array;
use function method_exists;

/**
 * Class GenericInstance
 */
final class ValueCasting
{
    /**
     * @var HydratorConfig
     */
    private HydratorConfig $config;

    /** @var ReflectionProperty $property */
    private ReflectionProperty $property;

    /**
     * @param ReflectionProperty $property
     * @param HydratorConfig|null $config
     */
    public function __construct(ReflectionProperty $property, HydratorConfig $config = null)
    {
        $this->property = $property;
        $this->config = $config ?? new HydratorConfig();
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     * @throws ClassNotFoundException|RuntimeException
     * @throws InvalidArgumentException
     */
    public function castAttribute(mixed $value): mixed
    {
        if ($this->property->type->isNullable && $value === null) {
            return null;
        }

        if (($value === '' || $value === []) && $this->property->convertEmptyToNull()) {
            return null;
        }

        if ($this->property->notTransform() || $this->property->type->name === TypeEnums::TYPE_MIXED) {
            return $value;
        }

        if (in_array($this->property->type->name, [TypeEnums::TYPE_STRING, TypeEnums::TYPE_INTEGER, TypeEnums::TYPE_FLOAT, TypeEnums::TYPE_BOOLEAN])) {
            return $this->castScalar($this->property->type->name, $value);
        }

        if ($this->property->type instanceof ArrayType) {
            return $this->castArray($value);
        }

        if ((is_string($value) || is_int($value)) && $this->property->type instanceof EnumType) {
            return $this->castEnum($value);
        }

        return (new Hydrator($this->config))
            ->create($this->property->type->name, $value);
    }


    /**
     * @param string $type
     * @param mixed $value
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    private function castScalar(string $type, mixed $value): mixed
    {

        $providedType = gettype($value);

        if ($this->property->type->name !== TypeEnums::TYPE_MIXED && !in_array($providedType, ['integer', 'string', 'boolean', 'double'])) {
            throw new InvalidArgumentException('Parameter `' . $this->property->name . '` expected type `' . $type . '`, `' . $providedType . '` provided');
        }

        return match ($type) {
            TypeEnums::TYPE_STRING => (string)$value,
            TypeEnums::TYPE_INTEGER => (int)$value,
            TypeEnums::TYPE_FLOAT => (float)$value,
            TypeEnums::TYPE_BOOLEAN => (bool)$value,
            TypeEnums::TYPE_MIXED => $value,
        };
    }

    /**
     * @param array<mixed>|mixed $value
     *
     * @return array<mixed>|mixed
     * @throws ClassNotFoundException
     * @throws InvalidArgumentException
     */
    private function castArray($value): mixed
    {
        if (!is_array($value) || $this->property->type->name === TypeEnums::TYPE_MIXED || !$this->property->type instanceof ArrayType) {
            return $value;
        }
        if (!$this->property->type->isScalarItems) {
            return array_map(fn($el) => (new Hydrator($this->config))->create($this->property->type->itemsType, $el), $value);
        }

        if ($this->property->type->itemsType === TypeEnums::TYPE_MIXED) {
            return $value;
        }

        return array_map(fn($item) => $this->castScalar($this->property->type->itemsType, $item), $value);
    }

    /**
     * @param int|string $value
     *
     * @return mixed
     */
    private function castEnum(int|string $value): mixed
    {
        $propertyClass = $this->property->type->name;
        if ($propertyClass && method_exists($propertyClass, 'from')) {
            /** @var \BackedEnum $propertyClass */
            return $propertyClass::from($value);
        }
        if (is_string($value)) {
            return constant($propertyClass . '::' . $value);
        }
        return $value;
    }
}
