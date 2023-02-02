<?php
declare(strict_types=1);

namespace Tests\Benchmark\DTO;

use ClassTransformer\Attributes\FieldAlias;
use ClassTransformer\Attributes\WritingStyle;

class UserDTO
{
    public int $id;
    public UserTypeEnum $type;
    #[FieldAlias('contact')]
    public ?string $email;
    public float $balance;

    #[WritingStyle(WritingStyle::STYLE_CAMEL_CASE)]
    public string $real_address;
}
