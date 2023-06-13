<?php

declare(strict_types=1);

namespace ClassTransformer;

/**
 * Class ClassTransformerConfig
 *
 * @psalm-api
 */
final class ClassTransformerConfig
{
    /** @var bool Cache mode enabled */
    public static bool $cacheEnabled = false;
    
    /** @var string Path to the cache directory */
    public static string $cachePath = __DIR__ . '/../.cache';
}
