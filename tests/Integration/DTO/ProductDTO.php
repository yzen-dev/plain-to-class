<?php

declare(strict_types=1);

namespace Tests\Integration\DTO;

class ProductDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public float $price
    ) {
    }
}
