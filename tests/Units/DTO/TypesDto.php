<?php

declare(strict_types=1);

namespace Tests\Units\DTO;

use ClassTransformer\Attributes\WritingStyle;

class TypesDto
{
    public ?int $nullableInt;

    public ?string $nullableString;
    public ?float $nullableFloat;
    public ?bool $nullableBool;

}
