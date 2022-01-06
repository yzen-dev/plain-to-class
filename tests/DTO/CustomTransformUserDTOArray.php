<?php

declare(strict_types=1);

namespace Tests\DTO;

class CustomTransformUserDTOArray
{
    public string $email;
    public string $username;

    /**
     * @return CustomTransformUserDTO
     */
    public static function transform($args)
    {
        $dto = new self();
        $dto->email = $args['login'];
        $dto->username = $args['fio'];
        return $dto;
    }
}
