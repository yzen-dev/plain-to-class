<?php

declare(strict_types=1);

namespace Tests\DTO;

class UnionTypeDTO
{
    /** @var int|string $id */
    public $id;

    /** @var string $email */
    public string $email;

    /** @var float|string $balance */
    public $balance;
}
