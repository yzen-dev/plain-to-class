<?php

namespace ClassTransformer\Reflection;

use ClassTransformer\Contracts\ReflectionClass;
use ClassTransformer\Contracts\ClassTransformable;
use ClassTransformer\CacheGenerator\CacheGenerator;
use ClassTransformer\Validators\ClassExistsValidator;
use ClassTransformer\Exceptions\ClassNotFoundException;

/**
 * Class RuntimeReflectionClass
 *
 * @psalm-api
 * @template T of ClassTransformable
 */
final class CacheReflectionClass implements ReflectionClass
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

        $cache = new CacheGenerator($this->class);

        if (!$cache->cacheExists()) {
            $cache->generate();
        }

        $class = $cache->get();

        $result = [];
        $class['properties'] = array_map(function ($item) {
            $property = new CacheReflectionProperty();

            $property->class = $item['class'];
            $property->name = $item['name'];
            $property->type = $item['type'];
            $property->types = $item['types'];
            $property->isScalar = $item['isScalar'];
            $property->hasSetMutator = $item['hasSetMutator'];
            $property->isArray = $item['isArray'];
            $property->isEnum = $item['isEnum'];
            $property->notTransform = $item['notTransform'];
            $property->isTransformable = $item['isTransformable'];
            $property->typeName = $item['typeName'];
            $property->docComment = $item['docComment'];
            $property->attributes = $item['attributes'];

            return $property;
        }, $class['properties']);

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
