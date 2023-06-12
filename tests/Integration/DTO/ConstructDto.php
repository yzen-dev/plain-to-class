<?php

declare(strict_types=1);

namespace Tests\Integration\DTO;

use ClassTransformer\Attributes\WritingStyle;
use Tests\Units\DTO\ColorEnum;

class ConstructDto
{
    public function __construct(
        public int $id,
        public ?string $email,
        #[WritingStyle(WritingStyle::STYLE_SNAKE_CASE)]
        public ?string $address,
        public float $balance,
        public ColorEnum $color
    ) {
    }
}
