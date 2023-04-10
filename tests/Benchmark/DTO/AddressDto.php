<?php
declare(strict_types=1);

namespace Tests\Benchmark\DTO;

use ClassTransformer\Attributes\FieldAlias;
use ClassTransformer\Attributes\WritingStyle;

class AddressDto
{
    public string $city;
    public string $street;
    public string $house;
}
