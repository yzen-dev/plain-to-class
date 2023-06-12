<?php

declare(strict_types=1);

namespace ClassTransformer\Reflection;

use ReflectionProperty;
use ReflectionException;
use ClassTransformer\Contracts\ReflectionClass;
use ClassTransformer\CacheGenerator\CacheGenerator;
use ClassTransformer\Validators\ClassExistsValidator;
use ClassTransformer\Exceptions\ClassNotFoundException;
use RuntimeException;

/**
 * Class RuntimeReflectionClass
 *
 * @psalm-api
 * @template T
 */
final class CacheReflectionClass implements ReflectionClass
{
    /** @var class-string $class */
    private string $class;

    /**
     * @var array<string,CacheReflectionProperty[]>
     */
    private static array $propertiesTypesCache = [];


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
     * @return CacheReflectionProperty[]
     * @throws ReflectionException|RuntimeException
     */
    public function getProperties(): array
    {
        if (isset(self::$propertiesTypesCache[$this->class])) {
            return self::$propertiesTypesCache[$this->class];
        }

        $cache = new CacheGenerator($this->class);

        /** @var CacheReflectionClass $class */
        if (!$cache->cacheExists()) {
            $class = $cache->generate();
        } else {
            $class = $cache->get();
        }

        return self::$propertiesTypesCache[$this->class] = $class['properties'];
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }
}
