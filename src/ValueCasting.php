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
 *
 * @template T of ClassTransformable
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
final class ValueCasting
{
    /** @var ReflectionProperty $property */
    private ReflectionProperty $property;


    /**
     * @param ReflectionClass $class
     * @param ArgumentsResource $argumentsResource
     */
    public function __construct(ReflectionProperty $property)
    {
        $this->property = $property;
    }
    
    /**
     * @param RuntimeReflectionProperty $property
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

        if ($this->property->isTransformable()) {
            $propertyClass = $this->property->getTypeName();

            /** @phpstan-ignore-next-line */
            return (new TransformBuilder($propertyClass, $value))->build();
        }
        
        return $value;
    }


    /**
     * @param RuntimeReflectionProperty $property
     * @param array<mixed>|mixed $value
     *
     * @return array<mixed>|mixed
     * @throws ClassNotFoundException
     */
    private function castArray($value): mixed
    {
        /** @var \ReflectionAttribute $arrayTypeAttr */
        $arrayTypeAttr = $this->property->getAttribute(ConvertArray::class);
        if ($arrayTypeAttr !== null) {
            
            $arrayType = $arrayTypeAttr->getArguments()[0];
        } else {
            $arrayType = TransformUtils::getClassFromPhpDoc($this->property->getDocComment());
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
     * @param RuntimeReflectionProperty $property
     * @param int|string $value
     *
     * @return mixed
     */
    private function castEnum(int|string $value)
    {
        $propertyClass = $this->property->getTypeName();
        if (method_exists($propertyClass, 'from')) {
            /** @var \BackedEnum $propertyClass */
            return $propertyClass::from($value);
        }

        return constant($propertyClass . '::' . $value);
    }
}
