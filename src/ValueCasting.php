<?php

declare(strict_types=1);

namespace ClassTransformer;

use ClassTransformer\Attributes\ConvertArray;
use ClassTransformer\Contracts\ReflectionProperty;
use ClassTransformer\Exceptions\ClassNotFoundException;

use ClassTransformer\Reflection\Types\TypeEnums;
use function method_exists;
use function is_array;
use function in_array;
use function array_map;

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
        if (($this->property->isScalar() && $this->property->getType() !== TypeEnums::TYPE_ARRAY) || $this->property->notTransform()) {
            return $this->castScalar($this->property->getType(), $value);
        }

        if ($this->property->getType() === TypeEnums::TYPE_ARRAY) {
            return $this->castArray($value);
        }

        if ((is_string($value) || is_int($value)) && $this->property->isEnum()) {
            return $this->castEnum($value);
        }

        if ($this->property->transformable()) {
            return (new Hydrator($this->config))
                ->create($this->property->getType(), $value);
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
        $arrayTypeAttr = $this->property->getAttributeArguments(ConvertArray::class);

        if ($arrayTypeAttr !== null && isset($arrayTypeAttr[0])) {
            $arrayType = $arrayTypeAttr[0];
        } else {
            $arrayType = TransformUtils::getClassFromPhpDoc($this->property->getDocComment());
        }

        if (empty($arrayType) || !is_array($value) || $arrayType === 'mixed') {
            return $value;
        }
        if (!in_array($arrayType, [TypeEnums::TYPE_INTEGER, TypeEnums::TYPE_FLOAT, TypeEnums::TYPE_STRING, TypeEnums::TYPE_BOOLEAN, TypeEnums::TYPE_MIXED])) {
            return array_map(fn($el) => (new Hydrator($this->config))->create($arrayType, $el), $value);
        }

        return array_map(fn($item) => $this->castScalar($arrayType, $item), $value);
    }

    /**
     * @param int|string $value
     *
     * @return mixed
     */
    private function castEnum(int|string $value): mixed
    {
        $propertyClass = $this->property->getType();
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
