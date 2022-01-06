<?php

declare(strict_types=1);

namespace Tests\DTO;

use ClassTransformer\Attributes\WritingStyle;

class WritingStyleSnakeCaseDTO
{
    #[WritingStyle(WritingStyle::STYLE_CAMEL_CASE)]
    public string $contact_fio;

    #[WritingStyle(WritingStyle::STYLE_ALL)]
    public string $contact_email;
}
