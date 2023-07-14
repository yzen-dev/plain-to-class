<?php

declare(strict_types=1);

namespace Tests\Units\DTO;

use ClassTransformer\Attributes\EmptyToNull;
use ClassTransformer\Attributes\WritingStyle;

class TypesDto
{
    public ?int $nullableInt;

    public ?string $nullableString;
    #[EmptyToNull]
    public ?string $emptyString;
    public ?float $nullableFloat;
    public ?bool $nullableBool;

}
