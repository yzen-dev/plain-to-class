<?php

declare(strict_types=1);

namespace Tests\Units\DTO;

use ClassTransformer\Attributes\FieldAlias;
use ClassTransformer\Attributes\WritingStyle;

class UserCacheableDTO
{
    public int $id;
    public ?string $email;
    
    #[FieldAlias('contact')]
    public $phone;

    /** @var array<int> $orders Order list */
    public array $orders;

    #[WritingStyle()]
    public ?string $addressOne;

    #[WritingStyle(WritingStyle::STYLE_CAMEL_CASE)]
    public ?string $address_two;

    #[WritingStyle(WritingStyle::STYLE_SNAKE_CASE)]
    public ?string $addressThree;

    #[WritingStyle(WritingStyle::STYLE_SNAKE_CASE, WritingStyle::STYLE_CAMEL_CASE)]
    public float $balance;

    public ColorEnum $color;
}
