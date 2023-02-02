<?php
declare(strict_types=1);

namespace Tests\Integration\DTO;

class BasketDTO
{
    /** @var array<\Tests\Integration\DTO\PurchaseDTO> $orders Order list */
    public array $orders;
}
