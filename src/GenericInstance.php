<?php

namespace ClassTransformer;

use ReflectionClass;
use ReflectionProperty;
use ReflectionException;
use ReflectionNamedType;
use ClassTransformer\Attributes\ConvertArray;
use ClassTransformer\Attributes\WritingStyle;
use ClassTransformer\Validators\ClassExistsValidator;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\Exceptions\ValueNotFoundException;

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

    /** @var array<mixed> $args */
    private array $args;

    /**
     * @param class-string $class
     *
     * @throws ClassNotFoundException
     */
    public function __construct(string $class)
    {
        new ClassExistsValidator($class);

        $this->class = $class;
    }

    /**
     * @param array<mixed>|object|null $args
     *
     * @return T
     * @throws ClassNotFoundException
     */
    public function transform(...$args): mixed
    {
        /** @var T $instance */
        $instance = new $this->class();

        $refInstance = new ReflectionClass($this->class);

        // Unpacking named arguments
        $inArgs = sizeof(func_get_args()) === 1 ? $args[0] : $args;

        if (is_object($inArgs)) {
            $inArgs = (array)$inArgs;
        }

        $this->args = $inArgs ?? [];

        foreach ($refInstance->getProperties() as $item) {
            $property = new GenericProperty($item);

            try {
                $value = $this->getValue($item);
            } catch (ValueNotFoundException) {
                continue;
            }

            if ($property->isScalar() || $property->notTransform()) {
                $instance->{$item->name} = $value;
                continue;
            }

            if ($property->isArray()) {
                $arrayTypeAttr = $item->getAttributes(ConvertArray::class);
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

            if ($property->getType() instanceof ReflectionNamedType) {
                /** @var class-string<T> $propertyClass */
                $propertyClass = $property->getType()->getName();

                if (enum_exists($property->getType()->getName())) {
                    $value = constant($propertyClass . '::' . $value);
                    $instance->{$item->name} = $value;
                    continue;
                }

                $instance->{$item->name} = (new TransformBuilder($propertyClass, $value))->build();
                continue;
            }
            $instance->{$item->name} = $value;
        }
        return $instance;
    }

    /**
     * @param ReflectionProperty $item
     *
     * @return mixed|object|array<mixed>|null
     * @throws ValueNotFoundException
     */
    private function getValue(ReflectionProperty $item)
    {
        if (array_key_exists($item->name, $this->args)) {
            return $this->args[$item->name];
        }

        $writingStyle = $item->getAttributes(WritingStyle::class);

        if (empty($writingStyle)) {
            throw new ValueNotFoundException();
        }
        foreach ($writingStyle as $style) {
            $styles = $style->getArguments();
            if (
                (in_array(WritingStyle::STYLE_SNAKE_CASE, $styles) || in_array(WritingStyle::STYLE_ALL, $styles)) &
                array_key_exists(TransformUtils::strToSnakeCase($item->name), $this->args)
            ) {
                return $this->args[TransformUtils::strToSnakeCase($item->name)];
            }
            if (
                (in_array(WritingStyle::STYLE_CAMEL_CASE, $styles) || in_array(WritingStyle::STYLE_ALL, $styles)) &
                array_key_exists(TransformUtils::strToCamelCase($item->name), $this->args)
            ) {
                return $this->args[TransformUtils::strToCamelCase($item->name)];
            }
        }
        throw new ValueNotFoundException();
    }
}
