<?php

namespace ClassTransformer\Reflection;

use ClassTransformer\ClassTransformable;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\GenericProperty;
use ClassTransformer\Validators\ClassExistsValidator;
use ReflectionClass as PhpReflectionClass;

/**
 * Class RuntimeReflectionClass
 *
 * @template T of ClassTransformable
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
final class RuntimeReflectionClass implements ReflectionClass
{
    /** @var class-string<T> $class */
    private string $class;

    /**
     * @var array<string,\ReflectionProperty[]>
     */
    private static $propertiesTypesCache = [];


    /**
     * @param class-string<T> $class
     *
     * @throws ClassNotFoundException
     */
    public function __construct(string $class)
    {
        new ClassExistsValidator($class);

        $this->class = $class;
    }

    /**
     * @return \ReflectionProperty[]
     * @throws \ReflectionException
     */
    public function getProperties(): array
    {
        if (isset(static::$propertiesTypesCache[$this->class])) {
            return static::$propertiesTypesCache[$this->class];
        }

        $refInstance = new PhpReflectionClass($this->class);

        $properties = $refInstance->getProperties();
        $result = [];
        foreach ($properties as $item) {
            $result [] = new GenericProperty($item);
        }
        
        return static::$propertiesTypesCache[$this->class] = $result;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }
}
