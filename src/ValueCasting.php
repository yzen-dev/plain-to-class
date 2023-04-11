<?php

namespace ClassTransformer;

use ClassTransformer\Attributes\ConvertArray;
use ClassTransformer\Contracts\ClassTransformable;
use ClassTransformer\Contracts\ReflectionClass;
use ClassTransformer\Contracts\ReflectionProperty;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\Exceptions\ValueNotFoundException;
use ClassTransformer\Reflection\RuntimeReflectionProperty;

/**
 * Class GenericInstance
 */
final class ValueCasting
{
    /** @var ReflectionProperty $property */
    private ReflectionProperty $property;


    /**
     * @param ReflectionProperty $property
     */
    public function __construct(ReflectionProperty $property)
    {
        $this->property = $property;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     * @throws ClassNotFoundException
     */
    public function castAttribute($value)
    {
        if ($this->property->isScalar() || $this->property->notTransform()) {
            return $value;
        }

        if ($this->property->isArray()) {
            return $this->castArray($value);
        }

        if ($this->property->isEnum() && (is_string($value) || is_int($value))) {
            return $this->castEnum($value);
        }

        $propertyClass = $this->property->transformable();
        if ($propertyClass) {
            return ClassTransformer::transform($propertyClass, $value);
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

        $array = [];
        if (!in_array($arrayType, ['int', 'float', 'string', 'bool', 'mixed'])) {
            foreach ($value as $el) {
                $array[] = ClassTransformer::transform($arrayType, $el);
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

        return constant($propertyClass . '::' . $value);
    }
}
