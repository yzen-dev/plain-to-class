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
    /** @var bool */
    public static bool $cache = false;
    public static string $cachePath = __DIR__ . '/../.cache';
}
