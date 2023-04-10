<?php

declare(strict_types=1);

namespace ClassTransformer\Attributes;

/**
 * @psalm-api
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class FieldAlias
{
    /**
     * @param string|array<string> $aliases
     */
    public function __construct(
        public string|array $aliases
    )
    {
    }
}
