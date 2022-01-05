<?php

declare(strict_types=1);

namespace ClassTransformer\Attributes;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class WritingStyle
{
    public const STYLE_ALL = 'ALL';
    public const STYLE_CAMEL_CASE = 'CAMEL_CASE';
    public const STYLE_SNAKE_CASE = 'SNAKE_CASE';

    /** @var array<string> */
    public array $styles = [];

    /**
     * @param string|array $styles
     */
    public function __construct(string|array $styles)
    {
        if (is_string($styles)) {
            $this->styles[] = $styles;
        } else {
            $this->styles = $styles;
        }
    }
}
