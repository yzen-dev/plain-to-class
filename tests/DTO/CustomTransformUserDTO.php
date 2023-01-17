<?php

declare(strict_types=1);

namespace Tests\DTO;

class CustomTransformUserDTO
{
    public string $email;
    public string $username;


    /**
     * @param $login
     * @param $fio
     * @return CustomTransformUserDTO
     */
    public function transform($login, $fio)
    {
        $this->email = $login;
        $this->username = $fio;
    }
}
