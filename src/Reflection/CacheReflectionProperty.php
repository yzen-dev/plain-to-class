<?php

namespace ClassTransformer\Reflection;

use ClassTransformer\Contracts\ReflectionProperty;
use ClassTransformer\TransformUtils;
use ClassTransformer\TransformBuilder;
use ClassTransformer\Attributes\ConvertArray;
use ClassTransformer\Exceptions\ClassNotFoundException;

/**
 * Class GenericProperty
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
final class CacheReflectionProperty implements \ClassTransformer\Contracts\ReflectionProperty
{
    /** @var ReflectionProperty $class */
    private ReflectionProperty $property;


    /**
     * @param ReflectionProperty $property
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
     * @param RuntimeReflectionProperty $property
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
     * @param RuntimeReflectionProperty $property
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
        return $this->property->hasSetMutator;
    }

    public function isEnum(): bool
    {
        // TODO: Implement isEnum() method.
    }

    public function isArray(): bool
    {
        // TODO: Implement isArray() method.
    }

    public function notTransform(): bool
    {
        // TODO: Implement notTransform() method.
    }

    public function isScalar(): bool
    {
        // TODO: Implement isScalar() method.
    }

    public function isTransformable(): bool
    {
        // TODO: Implement isTransformable() method.
    }

    public function getName(): string
    {
        // TODO: Implement getName() method.
    }

    public function getTypeName(): string
    {
        // TODO: Implement getTypeName() method.
    }

    public function getAttribute(string $name)
    {
        // TODO: Implement getAttribute() method.
    }
}
