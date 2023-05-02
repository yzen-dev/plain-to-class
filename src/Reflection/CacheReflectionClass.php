<?php

namespace ClassTransformer\Reflection;

use ReflectionProperty;
use ReflectionException;
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
     * @var array<string,ReflectionProperty[]>
     */
    private static array $propertiesTypesCache = [];


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
     * @return CacheReflectionProperty[]
     * @throws ReflectionException
     */
    public function getProperties(): array
    {
        if (isset(self::$propertiesTypesCache[$this->class])) {
            return self::$propertiesTypesCache[$this->class];
        }

        $cache = new CacheGenerator($this->class);

        if (!$cache->cacheExists()) {
            $class = $cache->generate();
        } else {
            $class = $cache->get();
        }

        $properties = array_map(
            static fn($item) => new CacheReflectionProperty(
                $item['class'],
                $item['name'],
                $item['type'],
                $item['types'],
                $item['isScalar'],
                $item['hasSetMutator'],
                $item['isArray'],
                $item['isEnum'],
                $item['notTransform'],
                $item['transformable'],
                $item['docComment'],
                $item['attributes'],
            ),
            $class['properties']
        );

        return self::$propertiesTypesCache[$this->class] = $properties;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }
}
