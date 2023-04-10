<?php

namespace Tests\Benchmark\DTO;

/**
 * Class ColorEnum
 */
enum UserTypeEnum: string
{
    case Admin = 'admin';
    case Client = 'client';
}
