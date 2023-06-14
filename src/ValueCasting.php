<?php

declare(strict_types=1);

namespace ClassTransformer;

use ClassTransformer\Attributes\ConvertArray;
use ClassTransformer\Contracts\ReflectionProperty;
use ClassTransformer\Enums\TypeEnums;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\Reflection\Types\ArrayType;
use ClassTransformer\Reflection\Types\EnumType;
use ClassTransformer\Reflection\Types\TransformableType;
use function array_map;
use function in_array;
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
     * @throws ClassNotFoundException
     */
    public function castAttribute(mixed $value): mixed
    {
        if (($this->property->type->isScalar && !$this->property->type instanceof ArrayType) || $this->property->notTransform()) {
            return $this->castScalar($this->property->type->name, $value);
        }

        if ($this->property->type instanceof ArrayType) {
            return $this->castArray($value);
        }

        if ((is_string($value) || is_int($value)) && $this->property->type instanceof EnumType) {
            return $this->castEnum($value);
        }

        if ($this->property->type instanceof TransformableType) {
            return (new Hydrator($this->config))
                ->create($this->property->type->name, $value);
        }

        return $value;
    }


    /**
     * @param string $type
     * @param mixed $value
     *
     * @return mixed
     */
    private function castScalar(string $type, mixed $value): mixed
    {
        return match ($type) {
            TypeEnums::TYPE_STRING => (string)$value,
            TypeEnums::TYPE_INTEGER => (int)$value,
            TypeEnums::TYPE_FLOAT => (float)$value,
            TypeEnums::TYPE_BOOLEAN => (bool)$value,
            default => $value
        };
    }

    /**
     * @param array<mixed>|mixed $value
     *
     * @return array<mixed>|mixed
     * @throws ClassNotFoundException
     */
    private function castArray($value): mixed
    {
        if (!is_array($value) || $this->property->type->name === TypeEnums::TYPE_MIXED) {
            return $value;
        }
        if (!$this->property->type->isScalarItems) {
            return array_map(fn($el) => (new Hydrator($this->config))->create($this->property->type->itemsType, $el), $value);
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
        if (is_string($propertyClass) && is_string($value)) {
            return constant($propertyClass . '::' . $value);
        }
        return $value;
    }
}
