<?php

namespace ClassTransformer;

use ClassTransformer\CacheGenerator\CacheGenerator;
use ClassTransformer\Validators\ClassExistsValidator;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\Reflection\RuntimeReflectionClass;

use ReflectionException;
use function method_exists;

/**
 * Class ClassRepository
 *
 * @template T of object
 * @author yzen.dev <yzen.dev@gmail.com>
 */
class Hydrator
{
    private HydratorConfig $config;

    /**
     * @var array<string,ClassRepository[]>
     */
    private static array $classRepositoryCache = [];

    /**
     */
    public function __construct(HydratorConfig $config = null)
    {
        $this->config = $config ?? new HydratorConfig();
    }

    /**
     */
    public static function init(HydratorConfig $config = null)
    {
        return new self($config);
    }

    /**
     * Create instance T class
     *
     * @param class-string<T> $class
     * @param iterable<mixed>|object ...$args
     *
     * @return null|T
     * @throws ClassNotFoundException
     */
    public function create(string $class, ...$args)
    {
        new ClassExistsValidator($class);

        $instance = $this->getInstance($class, ...$args);

        if (method_exists($instance, 'afterTransform')) {
            $instance->afterTransform();
        }

        return $instance;
    }

    /**
     * @param class-string<T> $class
     * @param array<iterable<mixed>> $args
     *
     * @return null|array<null>|array<T>
     * @throws ClassNotFoundException
     */
    public function createCollection(string $class, array $args): ?array
    {
        $result = [];
        foreach ($args as $item) {
            $result [] = $this->create($class, $item);
        }
        return $result;
    }

    /**
     * @param array<class-string<T>> $className
     * @param array<iterable<mixed>> $args
     *
     * @return null|array<null>|array<T>
     * @throws ClassNotFoundException
     */
    public function createMultiple(array $classes, array $args): ?array
    {
        $result = [];
        foreach ($classes as $key => $class) {
            $result [] = $this->create($class, $args[$key]);
        }
        return $result;
    }

    /**
     * @param class-string<T> $class
     * @param ...$args
     *
     * @return mixed
     * @throws ClassNotFoundException
     */
    private function getInstance(string $class, ...$args)
    {
        if (method_exists($class, 'transform')) {
            $instance = new $class();
            $instance->transform(...$args);
            return $instance;
        }

        return (new InstanceBuilder(
            $this->createClassRepository($class),
            new ArgumentsRepository(...$args),
            $this->config
        ))
            ->build();
    }

    /**
     * @param class-string<T> $class
     *
     * @return ClassRepository
     * @throws ClassNotFoundException|ReflectionException
     */
    private function createClassRepository(string $class): ClassRepository
    {
        if (isset(self::$classRepositoryCache[$class])) {
            return self::$classRepositoryCache[$class];
        }

        if ($this->config->cacheEnabled) {
            $cache = new CacheGenerator($class, $this->config);
            $repository = $cache->getClass();
        } else {
            $repository = new RuntimeReflectionClass($class);
        }

        return self::$classRepositoryCache[$class] = new ClassRepository(
            $class,
            $repository
        );
    }
}
