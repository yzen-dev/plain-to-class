<?php

declare(strict_types=1);

namespace ClassTransformer\Attributes;

use Attribute;

/**
 * Attribute for specifying the style of passed arguments
 *
 * @psalm-api
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
final class WritingStyle
{
    /** Check all possible styles */
    public const STYLE_ALL = 'ALL';
    /** Check camelCase styles */
    public const STYLE_CAMEL_CASE = 'CAMEL_CASE';
    /** Check snake_case styles */
    public const STYLE_SNAKE_CASE = 'SNAKE_CASE';

    /** @var array<string> */
    public array $styles = [];
}
