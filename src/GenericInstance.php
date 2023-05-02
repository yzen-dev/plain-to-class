<?php

namespace ClassTransformer;

use ClassTransformer\Contracts\ReflectionClass;
use ClassTransformer\Contracts\ClassTransformable;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\Exceptions\ValueNotFoundException;

/**
 * Class GenericInstance
 *
 * @psalm-api
 * @template T of ClassTransformable
 */
final class GenericInstance
{
    /** @var ReflectionClass $class */
    private ReflectionClass $class;

    /** @var ArgumentsResource $argumentsResource */
    private ArgumentsResource $argumentsResource;


    /**
     * @param ReflectionClass $class
     * @param ArgumentsResource $argumentsResource
     */
    public function __construct(ReflectionClass $class, ArgumentsResource $argumentsResource)
    {
        $this->class = $class;

        $this->argumentsResource = $argumentsResource;
    }

    /**
     * @return T
     * @throws ClassNotFoundException
     */
    public function transform(): mixed
    {
        $properties = $this->class->getProperties();
        /** @var T $genericInstance */
        $genericInstance = new ($this->class->getClass());

        foreach ($properties as $property) {
            try {
                $value = $this->argumentsResource->getValue($property);
            } catch (ValueNotFoundException) {
                continue;
            }

            if ($property->hasSetMutator()) {
                $genericInstance->{TransformUtils::mutationSetterToCamelCase($property->getName())}($value);
                continue;
            }

            $caster = new ValueCasting($property);
            $genericInstance->{$property->getName()} = $caster->castAttribute($value);
        }
        return $genericInstance;
    }
}
