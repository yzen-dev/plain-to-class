<?php
declare(strict_types=1);

namespace Tests\DTO;

class PurchaseDTO
{
    /** @var array<\Tests\DTO\ProductDTO> $products Product list */
    public array $products;

    /** @var \Tests\DTO\UserDTO $user */
    public UserDTO $user;
}
