<?php

declare(strict_types=1);

namespace Tests\DTO;

use ClassTransformer\Attributes\WritingStyle;

class WritingStyleCamelCaseDTO
{
    #[WritingStyle(WritingStyle::STYLE_SNAKE_CASE, WritingStyle::STYLE_CAMEL_CASE)]
    public string $contactFio;

    #[WritingStyle(WritingStyle::STYLE_SNAKE_CASE)]
    public string $contactEmail;
}
