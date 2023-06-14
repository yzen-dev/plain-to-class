<?php

declare(strict_types=1);

namespace Tests\Units\DTO;

use ClassTransformer\Attributes\WritingStyle;

class ExtendedDto
{
    public AbstractClass $mixed;
    
    public int $id;

    public ?string $email;

    #[WritingStyle(WritingStyle::STYLE_CAMEL_CASE)]
    public ?string $address_two;

    #[WritingStyle(WritingStyle::STYLE_SNAKE_CASE, WritingStyle::STYLE_CAMEL_CASE)]
    public float $balance;

    #[WritingStyle(WritingStyle::STYLE_CAMEL_CASE)]
    public ?string $testCase;

    #[WritingStyle(WritingStyle::STYLE_SNAKE_CASE)]
    public ?string $test_case;

    public ColorEnum $color;

    public bool $isBlocked;

    /** @var array<int>  */
    public array $intItems;

    /** @var array<float>  */
    public array $floatItems;

    /** @var array<string>  */
    public array $stringItems;

    /** @var array<bool>  */
    public array $boolItems;

    /** @var array<bool>  */
    public array $booleanItems;
    
    /** @var array<mixed>  */
    public array $mixedItems;
    
    public UserDto $user;

    public function setEmailAttribute($value)
    {
        $this->email = $value;
    }
}
