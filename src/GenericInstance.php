<?php

namespace ClassTransformer;

use ReflectionClass;
use ReflectionNamedType;
use ClassTransformer\Attributes\ConvertArray;
use ClassTransformer\Validators\ClassExistsValidator;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\Exceptions\ValueNotFoundException;

use function is_array;
use function constant;
use function method_exists;

/**
 * Class GenericInstance
 *
 * @template T of ClassTransformable
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
final class GenericInstance
{
    /** @var class-string<T> $class */
    private string $class;

    /** @var ArgumentsResource $argumentsResource */
    private ArgumentsResource $argumentsResource;

    /** @var T $genericInstance */
    private $genericInstance;

    /**
     * @var array<string,\ReflectionProperty[]>
     */
    private static $propertiesTypesCache = [];


    /**
     * @param class-string<T> $class
     *
     * @throws ClassNotFoundException
     */
    public function __construct(string $class, ArgumentsResource $argumentsResource)
    {
        new ClassExistsValidator($class);

        $this->class = $class;
        $this->argumentsResource = $argumentsResource;

        $this->genericInstance = new $this->class();
    }

    /**
     * @return \ReflectionProperty[]
     */
    public function getProperties(): array
    {
        if (isset(static::$propertiesTypesCache[$this->class])) {
            return static::$propertiesTypesCache[$this->class];
        }

        $refInstance = new ReflectionClass($this->class);
        return static::$propertiesTypesCache[$this->class] = $refInstance->getProperties();
    }

    /**
     * @return T
     * @throws ClassNotFoundException
     */
    public function transform(): mixed
    {
        $properties = $this->getProperties();
        foreach ($properties as $item) {
            $property = new GenericProperty($item);

            try {
                $value = $this->argumentsResource->getValue($property);
            } catch (ValueNotFoundException) {
                continue;
            }

            if ($this->hasSetMutator($property->name)) {
                $this->genericInstance->{TransformUtils::mutationSetterToCamelCase($property->name)}($value);
                continue;
            }

            $this->genericInstance->{$property->name} = $this->castAttribute($property, $value);
        }
        return $this->genericInstance;
    }

    /**
     * @param GenericProperty $property
     * @param mixed $value
     *
     * @return mixed
     * @throws ClassNotFoundException
     */
    private function castAttribute(GenericProperty $property, $value)
    {
        if ($property->isScalar || $property->notTransform()) {
            return $value;
        }

        if ($property->isArray()) {
            return $this->castArray($property, $value);
        }

        if ($property->isEnum() && (is_string($value) || is_int($value))) {
            return $this->castEnum($property, $value);
        }

        if ($property->type instanceof ReflectionNamedType) {
            /** @var class-string<T> $propertyClass */
            $propertyClass = $property->type->getName();

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
    private function castArray(GenericProperty $property, $value): mixed
    {
        $arrayTypeAttr = $property->getAttribute(ConvertArray::class);
        if ($arrayTypeAttr !== null) {
            $arrayType = $arrayTypeAttr->getArguments()[0];
        } else {
            $arrayType = TransformUtils::getClassFromPhpDoc($property->getDocComment());
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
    private function castEnum(GenericProperty $property, int|string $value)
    {
        /** @phpstan-ignore-next-line */
        $propertyClass = $property->type->getName();
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
    public function hasSetMutator(string $key): bool
    {
        return method_exists($this->class, TransformUtils::mutationSetterToCamelCase($key));
    }
}
