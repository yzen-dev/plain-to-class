<?php

declare(strict_types=1);

namespace ClassTransformer\Reflection;

use ClassTransformer\Contracts\ReflectionClass;
use ClassTransformer\Validators\ClassExistsValidator;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ReflectionClass as PhpReflectionClass;

/**
 * Class RuntimeReflectionClass
 *
 * @psalm-api
 * @template T
 */
final class RuntimeReflectionClass implements ReflectionClass
{
    /** @var class-string $class */
    private string $class;

    /**
     * @var array<string,RuntimeReflectionProperty[]>
     */
    private static $propertiesTypesCache = [];


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
     * @return RuntimeReflectionProperty[]
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
            $result [] = new RuntimeReflectionProperty($item);
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
