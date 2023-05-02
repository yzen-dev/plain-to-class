<?php

declare(strict_types=1);

namespace Tests\Integration\DTO;

use ClassTransformer\Attributes\ConvertArray;

class PurchaseDTO
{
    #[ConvertArray(ProductDTO::class)]
    public array $products;

    /** @var UserDTO $user */
    public UserDTO $user;

    /** @var array<UserDTO> $orders Order list */
    public array $clients;
}
