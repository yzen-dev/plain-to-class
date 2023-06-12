<?php

declare(strict_types=1);

namespace ClassTransformer\CacheGenerator;

use ClassTransformer\ClassTransformerConfig;
use ClassTransformer\Reflection\CacheReflectionProperty;
use ReflectionClass;
use RuntimeException;
use ReflectionException;
use ReflectionNamedType;
use ClassTransformer\Reflection\RuntimeReflectionProperty;

/**
 * Class CacheGenerator
 *
 * @template TClass
 */
class CacheGenerator
{
    private const DIR_PERMISSION = 0777;

    /** @psalm-param class-string<TClass> $class */
    private string $class;

    /**
     * @param class-string<TClass> $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * @return array{properties: array<CacheReflectionProperty>}
     * @throws ReflectionException|RuntimeException
     */
    public function generate(): array
    {
        $this->makeCacheDir(ClassTransformerConfig::$cachePath);
        $class = str_replace('\\', '_', $this->class);

        $refInstance = new ReflectionClass($this->class);

        $properties = $refInstance->getProperties();

        $cache = [
            'properties' => array_map(fn($el) => $this->convertToCacheProperty(new RuntimeReflectionProperty($el)), $properties)
        ];

        $path = ClassTransformerConfig::$cachePath . DIRECTORY_SEPARATOR . $class . '.cache.php';
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
     * @return array
     */
    public function get(): array
    {
        $class = str_replace('\\', '_', $this->class);
        return unserialize(file_get_contents(ClassTransformerConfig::$cachePath . DIRECTORY_SEPARATOR . $class . '.cache.php'));
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
        $concurrentDirectory = ClassTransformerConfig::$cachePath;
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
