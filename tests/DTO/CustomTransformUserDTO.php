<?php

declare(strict_types=1);

namespace Tests\DTO;

class CustomTransformUserDTO
{
    public string $email;
    public string $username;

    public static function transform($args)
    {
        $dto = new self();
        $dto->email = $args['login'];
        $dto->username = $args['fio'];
        return $dto;
    }
}
