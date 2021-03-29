<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use ClassTransformer\ClassTransformer;
use Tests\DTO\CustomTransformUserDTO;

/**
 * Class CustomTransformerTest
 * @package Tests
 */
class CustomTransformerTest extends TestCase
{

    public function testCustomTransform(): void
    {
        $data = [
            'login' => 'test-login',
            'fio' => 'Corey',
        ];
        $userDTO = ClassTransformer::transform(CustomTransformUserDTO::class, $data);

        self::assertInstanceOf(CustomTransformUserDTO::class, $userDTO);

        self::assertFalse(isset($userDTO->login));
        self::assertFalse(isset($userDTO->fio));

        self::assertEquals($data['login'], $userDTO->email);
        self::assertEquals($data['fio'], $userDTO->username);
    }
}
