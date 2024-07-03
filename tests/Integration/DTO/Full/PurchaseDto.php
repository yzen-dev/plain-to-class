<?php

declare(strict_types=1);

namespace Tests\Integration\DTO\Full;

use ClassTransformer\Attributes\ConvertArray;
use Tests\Integration\DTO\Full\Address\AddressClean;

class PurchaseDto
{
    #[ConvertArray(ProductDto::class)]
    /** @var array<ProductDto> */
    public array $products;

    /** @var UserDto $user */
    public UserDto $user;

    public AddressClean $address;

    public \DateTime $createdAt;
}
