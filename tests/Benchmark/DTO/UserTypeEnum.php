<?php

namespace Tests\Benchmark\DTO;

/**
 * Class ColorEnum
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
enum UserTypeEnum: string
{
    case Admin = 'admin';
    case Client = 'client';
}
