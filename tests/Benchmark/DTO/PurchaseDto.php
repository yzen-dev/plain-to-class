<?php

declare(strict_types=1);

namespace Tests\Benchmark\DTO;

use ClassTransformer\Attributes\ConvertArray;

class PurchaseDto
{
    #[ConvertArray(ProductDto::class)]
    public array $products;

    /** @var UserDto $user */
    public UserDto $user;
    public AddressDto $address;
    public \DateTime $createdAt;
}
