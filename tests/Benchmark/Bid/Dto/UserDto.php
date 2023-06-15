<?php
declare(strict_types=1);

namespace Tests\Benchmark\Bid\Dto;

use ClassTransformer\Attributes\FieldAlias;
use ClassTransformer\Attributes\WritingStyle;

class UserDto
{
    public int $id;
    
    public UserTypeEnum $type;
    
    #[FieldAlias('contact')]
    public ?string $email;
    
    public float $balance;
    
    public \DateTime $createdAt;

    #[WritingStyle(WritingStyle::STYLE_CAMEL_CASE)]
    public string $real_address;
}
