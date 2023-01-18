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
    /** @var class-string $class */
    private string $class;

    /** @var ArgumentsResource $argumentsResource */
    private ArgumentsResource $argumentsResource;


    /**
     * @param class-string $class
     *
     * @throws ClassNotFoundException
     */
    public function __construct(string $class, ArgumentsResource $argumentsResource)
    {
        new ClassExistsValidator($class);

        $this->class = $class;
        $this->argumentsResource = $argumentsResource;
    }

    /**
     * @param array<mixed>|object|null $args
     *
     * @return T
     * @throws ClassNotFoundException
     */
    public function transform(): mixed
    {
        /** @var T $instance */
        $instance = new $this->class();

        $refInstance = new ReflectionClass($this->class);

        foreach ($refInstance->getProperties() as $item) {
            $property = new GenericProperty($item);

            try {
                $value = $this->argumentsResource->getValue($property);
            } catch (ValueNotFoundException) {
                continue;
            }

            if ($property->isScalar || $property->notTransform()) {
                $instance->{$item->name} = $value;
                continue;
            }

            if ($property->isArray()) {
                $arrayTypeAttr = $property->getAttributes(ConvertArray::class);
                if (!empty($arrayTypeAttr)) {
                    $arrayType = $arrayTypeAttr[0]->getArguments()[0];
                } else {
                    $arrayType = TransformUtils::getClassFromPhpDoc($property->getDocComment());
                }

                if (!empty($arrayType) && !empty($value) && is_array($value) && !TransformUtils::propertyIsScalar($arrayType)) {
                    foreach ($value as $el) {
                        $instance->{$item->name}[] = (new TransformBuilder($arrayType, $el))->build();
                    }
                    continue;
                }

                $instance->{$item->name} = $value;
                continue;
            }

            if ($property->type instanceof ReflectionNamedType) {
                /** @var class-string<T> $propertyClass */
                $propertyClass = $property->type->getName();

                if ($property->isEnum()) {
                    if (method_exists($propertyClass, 'from')) {
                        /** @var \UnitEnum $propertyClass */
                        $instance->{$item->name} = $propertyClass::from($value);
                    } else {
                        $instance->{$item->name} = constant($propertyClass . '::' . $value);
                    }
                    continue;
                }

                $instance->{$item->name} = (new TransformBuilder($propertyClass, $value))->build();
                continue;
            }
            $instance->{$item->name} = $value;
        }
        return $instance;
    }
}
