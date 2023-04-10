<?php

namespace ClassTransformer;

use ClassTransformer\Contracts\ClassTransformable;
use ClassTransformer\Reflection\CacheReflectionClass;
use ClassTransformer\Reflection\RuntimeReflectionClass;
use ClassTransformer\Validators\ClassExistsValidator;
use ClassTransformer\Exceptions\ClassNotFoundException;

/**
 * Class TransformBuilder
 *
 * @template T of ClassTransformable
 * @package ClassTransformer
 */
final class TransformBuilder
{
    /**
     * @var class-string<T> $class
     */
    private string $class;

    /** @var iterable<mixed> $args */
    private $args;

    /**
     * @param class-string<T> $class
     * @param iterable<mixed> ...$args
     *
     * @throws ClassNotFoundException
     */
    public function __construct(string $class, ...$args)
    {
        new ClassExistsValidator($class);

        $this->class = $class;
        $this->args = $args;
    }

    /**
     * @return T
     * @throws ClassNotFoundException
     */
    public function build()
    {
        if (method_exists($this->class, 'transform')) {
            /** @var T $instance */
            $instance = new $this->class();
            $instance->transform(...$this->args);
        } else {
            if (ClassTransformerConfig::$cache) {
                $reflection = new CacheReflectionClass($this->class);
            } else {
                $reflection = new RuntimeReflectionClass($this->class);
            }
            $generic = new GenericInstance($reflection, new ArgumentsResource(...$this->args));
            /** @var T $instance */
            $instance = $generic->transform();
        }

        if (method_exists($instance, 'afterTransform')) {
            $instance->afterTransform();
        }

        return $instance;
    }

}
