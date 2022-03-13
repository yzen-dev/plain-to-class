<?php

declare(strict_types=1);

namespace Tests\DTO;

class PurchaseDTO
{
    /** @var array<\Tests\DTO\ProductDTO> $products */
    public array $products;

    /** @var UserDTO $user */
    public UserDTO $user;
}
