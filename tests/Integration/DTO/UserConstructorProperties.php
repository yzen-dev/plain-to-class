<?php
declare(strict_types=1);

namespace Tests\Integration\DTO;

class UserConstructorProperties
{
    public function __construct(
        public int $id,

        public ?string $email,
        public float $balance,
        public mixed $mixed,
        public bool $isBlocked,
    ) {
    }
}
