<?php

declare(strict_types=1);

namespace Tests\Benchmark\Bid\Dto;

use ClassTransformer\Attributes\ConvertArray;
use Tests\Benchmark\Bid\Dto\Address\AddressClean;

class PurchaseDto
{
    #[ConvertArray(ProductDto::class)]
    public array $products;

    /** @var UserDto $user */
    public UserDto $user;

    public AddressClean $address;

    public \DateTime $createdAt;
}
