<?php
declare(strict_types=1);

namespace Tests\Integration\DTO;

class CustomSetterAttibuteDTO
{
    public int $id;
    public string $real_address;
    public string $userName;

    public function setRealAddressAttribute(string $value)
    {
        $this->real_address = strtolower($value);
    }

    public function setUserNameAttribute(string $value)
    {
        $this->userName = strtoupper($value);
    }
}
