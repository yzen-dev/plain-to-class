<?php

declare(strict_types=1);

namespace ClassTransformer\Attributes;

/**
 *
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class FieldAlias
{
    /**
     * @param string|array $aliases
     */
    public function __construct(string|array $aliases)
    {
    }
}
