<?php

declare(strict_types=1);

namespace ClassTransformer\CacheGenerator;

use ReflectionClass;
use RuntimeException;
use ReflectionException;
use ReflectionNamedType;
use ClassTransformer\HydratorConfig;
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
class CacheGenerator
{
    private const DIR_PERMISSION = 0777;

    private HydratorConfig $config;

    /** @psalm-param class-string<TClass> $class */
    private string $class;

    /**
     * @param class-string<TClass> $class
     */
    public function __construct(string $class, HydratorConfig $config = null)
    {
        $this->class = $class;
        $this->config = $config ?? new HydratorConfig();
    }


    /**
     * @param string $class
     *
     * @return CacheReflectionClass
     * @throws ReflectionException
     * @throws ClassNotFoundException
     */
    public function getClass(): CacheReflectionClass
    {
        if (!$this->cacheExists()) {
            $classCache = $this->generate();
        } else {
            $classCache = $this->get();
        }
        return new CacheReflectionClass($this->class, $classCache['properties']);
    }

    /**
     * @return array{properties: array<CacheReflectionProperty>}
     * @throws ReflectionException|RuntimeException
     */
    public function generate(): array
    {
        $this->makeCacheDir($this->config->cachePath);
        $class = str_replace('\\', '_', $this->class);

        $refInstance = new ReflectionClass($this->class);

        $properties = $refInstance->getProperties();

        $cache = [
            'properties' => array_map(fn($el) => $this->convertToCacheProperty(new RuntimeReflectionProperty($el)), $properties)
        ];

        $path = $this->config->cachePath . DIRECTORY_SEPARATOR . $class . '.cache.php';
        file_put_contents($path, serialize($cache));
        return $cache;
    }

    /**
     * @param RuntimeReflectionProperty $property
     *
     * @return CacheReflectionProperty
     */
    private function convertToCacheProperty(RuntimeReflectionProperty $property): CacheReflectionProperty
    {
        if ($property->type instanceof ReflectionNamedType) {
            $type = $property->type->getName();
        } else {
            $type = $property->type;
        }

        return new CacheReflectionProperty(
            $property->class,
            $property->name,
            (string)$type,
            $property->types,
            $property->isScalar,
            $property->hasSetMutator(),
            $property->isArray(),
            $property->isEnum(),
            $property->notTransform(),
            $property->transformable(),
            $property->getDocComment(),
            $this->getArguments($property),
        );
    }

    /**
     * @param RuntimeReflectionProperty $property
     *
     * @return array<array-key, array<mixed>>
     */
    private function getArguments(RuntimeReflectionProperty $property): array
    {
        $attrs = $property->property->getAttributes();
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
        $class = str_replace('\\', '_', $this->class);
        return unserialize(file_get_contents($this->config->cachePath . DIRECTORY_SEPARATOR . $class . '.cache.php'));
    }

    /**
     * @return bool
     */
    public function cacheExists(): bool
    {
        $class = str_replace('\\', '_', $this->class);
        return file_exists('./.cache/' . $class . '.cache.php');
    }

    /**
     * @param string|null $path
     *
     * @return void
     * @throws RuntimeException
     */
    private function makeCacheDir(?string $path): void
    {
        $concurrentDirectory = $this->config->cachePath;
        if (
            empty($path) ||
            (
                !file_exists($concurrentDirectory) &&
                !mkdir($concurrentDirectory, self::DIR_PERMISSION, true) &&
                !is_dir($concurrentDirectory)
            )
        ) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }
}
