<?php
declare(strict_types=1);

namespace Tests\Integration\DTO;

class UserAfterTransformDTO
{
    public int $id;
    public ?string $email;
    public float $balance;

    public function afterTransform()
    {
        $this->balance = 777;
    }
}
