<?php

declare(strict_types=1);

namespace ClassTransformer\Reflection;

use RuntimeException;
use ReflectionException;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\Contracts\ReflectionClassRepository;

/**
 * Class RuntimeReflectionClass
 *
 * @psalm-api
 * @template T
 */
final class CacheReflectionClass implements ReflectionClassRepository
{
    /** @var class-string $class */
    private string $class;

    /** @var array CacheReflectionProperty[] */
    private array $properties;

    /**
     * @param class-string $class
     */
    public function __construct(string $class, array $properties)
    {
        $this->class = $class;
        $this->properties = $properties;
    }

    /**
     * @return CacheReflectionProperty[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }
}
