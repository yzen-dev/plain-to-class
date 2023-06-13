<?php

declare(strict_types=1);

namespace ClassTransformer;

use ClassTransformer\Attributes\ConvertArray;
use ClassTransformer\Contracts\ReflectionProperty;
use ClassTransformer\Exceptions\ClassNotFoundException;

use function method_exists;
use function is_array;
use function in_array;
use function array_map;

/**
 * Class GenericInstance
 */
final class ValueCasting
{

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
        if ($this->property->isScalar() || $this->property->notTransform()) {
            return match ($this->property->getType()) {
                'string' => (string)$value,
                'int' => (int)$value,
                'float' => (float)$value,
                'bool' => (bool)$value,
                default => $value
            };
        }

        if ($this->property->isArray()) {
            return $this->castArray($value);
        }

        if ((is_string($value) || is_int($value)) && $this->property->isEnum()) {
            return $this->castEnum($value);
        }

        $propertyClass = $this->property->transformable();
        if ($propertyClass) {
            return (new Hydrator($this->config))
                ->create($propertyClass, $value);
        }

        return $value;
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
        if (!in_array($arrayType, ['int', 'float', 'string', 'bool', 'boolean', 'mixed'])) {
            return array_map(fn($el) => (new Hydrator($this->config))->create($arrayType, $el), $value);
        }

        return array_map(static fn($el) => match ($arrayType) {
            'string' => (string)$el,
            'int' => (int)$el,
            'float' => (float)$el,
            'bool', 'boolean' => (bool)$el,
            default => $el
        }, $value);
    }

    /**
     * @param int|string $value
     *
     * @return mixed
     */
    private function castEnum(int|string $value): mixed
    {
        $propertyClass = $this->property->transformable();
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
