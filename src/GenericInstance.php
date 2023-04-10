<?php

namespace ClassTransformer;

use ClassTransformer\Reflection\ReflectionClass;
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
    /** @var ReflectionClass $class */
    private ReflectionClass $class;

    /** @var ArgumentsResource $argumentsResource */
    private ArgumentsResource $argumentsResource;

    /** @var T $genericInstance */
    private $genericInstance;


    /**
     * @param ReflectionClass $class
     * @param ArgumentsResource $argumentsResource
     */
    public function __construct(ReflectionClass $class, ArgumentsResource $argumentsResource)
    {
        $this->class = $class;
        
        $this->argumentsResource = $argumentsResource;

        $this->genericInstance = new ($class->getClass());
    }

    /**
     * @return T
     * @throws ClassNotFoundException
     */
    public function transform(): mixed
    {
        $properties = $this->class->getProperties();

        foreach ($properties as $property) {
            try {
                $value = $this->argumentsResource->getValue($property);
            } catch (ValueNotFoundException) {
                continue;
            }

            if ($property->hasSetMutator()) {
                $this->genericInstance->{TransformUtils::mutationSetterToCamelCase($property->name)}($value);
                continue;
            }

            $this->genericInstance->{$property->name} = $property->castAttribute($value);
        }
        return $this->genericInstance;
    }


}
