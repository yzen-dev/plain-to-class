<?php

declare(strict_types=1);

namespace ClassTransformer;

/**
 * Class HydratorConfig
 *
 * @psalm-api
 */
final class HydratorConfig
{
    /** @var bool Cache mode enabled */
    public bool $cacheEnabled = false;
    
    /** @var string Path to the cache directory */
    public string $cachePath = __DIR__ . '/../.cache';
    
    public function __construct(
        ?bool $cacheEnabled = null,
        ?string $cachePath = null
    )
    {
        $this->cacheEnabled = $cacheEnabled ?? ClassTransformerConfig::$cacheEnabled;
        $this->cachePath = $cachePath ?? ClassTransformerConfig::$cachePath;
    }
}
