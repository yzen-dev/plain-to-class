<?php

declare(strict_types=1);

namespace Tests\DTO;

class CustomTransformUserDTOArray
{
    public string $email;
    public string $username;
    
    public function transform($args)
    {
        $this->email = $args['login'];
        $this->username = $args['fio'];
    }
}
