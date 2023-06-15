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
    public bool $cacheEnabled;

    /** @var string Path to the cache directory */
    public string $cachePath;

    /**
     * @param bool|null $cacheEnabled
     * @param string|null $cachePath
     */
    public function __construct(
        ?bool $cacheEnabled = null,
        ?string $cachePath = null
    ) {
        $this->cacheEnabled = $cacheEnabled ?? false;
        $this->cachePath = $cachePath ?? __DIR__ . '/../.cache';
    }
}
