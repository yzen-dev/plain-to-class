<?php

declare(strict_types=1);

namespace ClassTransformer\CacheGenerator;

use ReflectionClass;
use RuntimeException;
use ClassTransformer\HydratorConfig;
use ClassTransformer\Attributes\FieldAlias;
use ClassTransformer\Reflection\CacheReflectionClass;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\Reflection\CacheReflectionProperty;
use ClassTransformer\Reflection\RuntimeReflectionProperty;

use function mkdir;
use function is_dir;
use function str_replace;
use function unserialize;
use function file_exists;
use function file_get_contents;

/**
 * Class CacheGenerator
 *
 * @template TClass
 */
final class CacheGenerator
{
    /**
     *
     */
    private const DIR_PERMISSION = 0777;

    /**
     * @var HydratorConfig
     */
    private HydratorConfig $config;

    /** @psalm-param class-string<TClass> $class */
    private string $class;

    /** @var string Path to cache file */
    private string $path;

    /**
     * @param class-string<TClass> $class
     */
    public function __construct(string $class, HydratorConfig $config = null)
    {
        $this->config = $config ?? new HydratorConfig();
        $this->class = $class;
        $cacheFile = str_replace('\\', '_', $this->class);
        $this->path = $this->config->cachePath . DIRECTORY_SEPARATOR . $cacheFile . '.cache.php';
    }

    /**
     * @template T
     *
     * @param class-string<T> $class
     */
    public static function create(string $class, HydratorConfig $config = null): CacheGenerator
    {
        return new self($class, $config);
    }


    /**
     * @return CacheReflectionClass
     * @throws ClassNotFoundException|RuntimeException
     */
    public function getClass(): CacheReflectionClass
    {
        /** @infection-ignore-all */
        if (!$this->cacheExists()) {
            $classCache = $this->generate();
        } else {
            $classCache = $this->get();
        }
        return new CacheReflectionClass($this->class, $classCache['properties']);
    }

    /**
     * @return array{properties: array<CacheReflectionProperty>}
     * @throws ClassNotFoundException|RuntimeException
     */
    public function generate(): array
    {
        $this->makeCacheDir();

        if (!class_exists($this->class)) {
            throw new ClassNotFoundException("Class $this->class not found. Please check the class path you specified.");
        }

        $refInstance = new ReflectionClass($this->class);

        $properties = $refInstance->getProperties();

        $cache = [
            'properties' => array_map(fn($el) => $this->convertToCacheProperty(new RuntimeReflectionProperty($el)), $properties)
        ];

        file_put_contents($this->path, serialize($cache));

        return $cache;
    }

    /**
     * @param RuntimeReflectionProperty $property
     *
     * @return CacheReflectionProperty
     */
    private function convertToCacheProperty(RuntimeReflectionProperty $property): CacheReflectionProperty
    {
        $args = $this->getArguments($property);

        return new CacheReflectionProperty(
            $property->class,
            $property->name,
            $property->type,
            $property->hasSetMutator(),
            $property->notTransform(),
            $property->getDocComment(),
            $args,
            $this->getAliases($args),
        );
    }

    /**
     * @param array $args
     *
     * @return array<array-key,string>
     */
    public function getAliases(array $args = []): array
    {
        $aliases = $args[FieldAlias::class] ?? null;

        if (empty($aliases) || !is_array($aliases)) {
            return [];
        }

        $aliases = $aliases[0];

        if (is_string($aliases)) {
            $aliases = [$aliases];
        }
        return $aliases;
    }

    /**
     * @param RuntimeReflectionProperty $property
     *
     * @return array<array-key, array<mixed>>
     */
    private function getArguments(RuntimeReflectionProperty $property): array
    {
        $attrs = $property->reflectionProperty->getAttributes();
        $attributes = [];
        foreach ($attrs as $attr) {
            $attributes[$attr->getName()] = $attr->getArguments();
        }
        return $attributes;
    }

    /**
     * @return array{properties: array<CacheReflectionProperty>}
     */
    public function get(): array
    {
        return unserialize(file_get_contents($this->path));
    }

    /**
     * @return bool
     */
    public function cacheExists(): bool
    {
        return file_exists($this->path);
    }

    /**
     * @return void
     * @throws RuntimeException
     */
    private function makeCacheDir(): void
    {
        if (
            (!file_exists($this->config->cachePath) && !mkdir($concurrentDirectory = $this->config->cachePath, self::DIR_PERMISSION, true) && !is_dir($concurrentDirectory))
            ||
            !is_dir($this->config->cachePath)
        ) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $this->config->cachePath));
        }
    }
}
