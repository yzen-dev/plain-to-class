<?php

namespace ClassTransformer;

use RuntimeException;
use ReflectionException;
use ClassTransformer\CacheGenerator\CacheGenerator;
use ClassTransformer\Validators\ClassExistsValidator;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\Reflection\RuntimeReflectionClass;

use function method_exists;

/**
 * Class ClassRepository
 *
 * @psalm-api
 * @template T
 * @author yzen.dev <yzen.dev@gmail.com>
 */
final class Hydrator
{
    /**
     * @var HydratorConfig
     */
    private HydratorConfig $config;

    /**
     * @var array<string,ClassRepository>
     */
    private static array $classRepositoryCache = [];

    /**
     */
    public function __construct(HydratorConfig $config = null)
    {
        $this->config = $config ?? new HydratorConfig();
    }

    /**
     * @param HydratorConfig|null $config
     *
     * @return Hydrator
     */
    public static function init(HydratorConfig $config = null): self
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
     * @throws ClassNotFoundException|RuntimeException
     */
    public function create(string $class, ...$args): mixed
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
     * @throws ClassNotFoundException|ReflectionException
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
     * @throws ClassNotFoundException|ReflectionException
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
     * @param iterable<mixed>|object ...$args
     *
     * @return mixed
     * @throws ClassNotFoundException|RuntimeException
     */
    private function getInstance(string $class, ...$args): mixed
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
     * @throws ClassNotFoundException|RuntimeException
     */
    private function createClassRepository(string $class): ClassRepository
    {
        if (isset(self::$classRepositoryCache[$class])) {
            return self::$classRepositoryCache[$class];
        }

        if ($this->config->cacheEnabled) {
            $repository = CacheGenerator::create($class, $this->config)->getClass();
        } else {
            $repository = new RuntimeReflectionClass($class);
        }

        return self::$classRepositoryCache[$class] = new ClassRepository(
            $class,
            $repository
        );
    }
}
