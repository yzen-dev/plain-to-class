<?php

declare(strict_types=1);

namespace Tests\DTO;

use ClassTransformer\Attributes\WritingStyle;

class WritingStyleEmpyDTO
{
    /**
     * @var string $contactFio
     * @writingStyle<>
     */
    public string $contactFio;

    /**
     * @var string $contactEmail
     * @writingStyle<WritingStyle::STYLE_CAMEL_CASE> 
     */
    public string $contactEmail;
}
