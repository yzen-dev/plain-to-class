<?php

declare(strict_types=1);

namespace Tests\Integration\DTO;

class UnionTypeDTO
{
    public int|string $id;
    public string $email;
    public float|string $balance;
}
