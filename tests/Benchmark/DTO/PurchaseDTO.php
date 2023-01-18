<?php

declare(strict_types=1);

namespace Tests\Benchmark\DTO;

use ClassTransformer\Attributes\ConvertArray;

class PurchaseDTO
{
    #[ConvertArray(ProductDTO::class)]
    public array $products;

    /** @var UserDTO $user */
    public UserDTO $user;
}
