<?php

declare(strict_types=1);

namespace Tests\DTO;

use ClassTransformer\Attributes\WritingStyle;

class WritingStyleSnakeCaseDTO
{
    /**
     * @var string $contact_fio
     * @writingStyle<WritingStyle::STYLE_CAMEL_CASE>
     */
    public string $contact_fio;

    /**
     * @var string $contact_email
     * @writingStyle<WritingStyle::STYLE_ALL>
     */
    public string $contact_email;
}
