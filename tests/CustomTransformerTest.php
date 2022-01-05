<?php

declare(strict_types=1);

namespace Tests;

use ClassTransformer\Exceptions\ClassNotFoundException;
use PHPUnit\Framework\TestCase;
use ClassTransformer\ClassTransformer;
use ReflectionException;
use Tests\DTO\CustomTransformUserDTO;
use Tests\DTO\CustomTransformUserDTOArray;

/**
 * Class CustomTransformerTest
 * @package Tests
 */
class CustomTransformerTest extends TestCase
{
    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testCustomTransform(): void
    {
        $data = [
            'login' => 'test-login',
            'fio' => 'Corey',
        ];
        $userDTO = ClassTransformer::transform(CustomTransformUserDTOArray::class, $data);

        self::assertInstanceOf(CustomTransformUserDTOArray::class, $userDTO);

        self::assertFalse(isset($userDTO->login));
        self::assertFalse(isset($userDTO->fio));

        self::assertEquals('test-login', $userDTO->email);
        self::assertEquals('Corey', $userDTO->username);
    }

    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testCustomTransformPhp8(): void
    {
        $userDTO = ClassTransformer::transform(CustomTransformUserDTO::class, login: 'test-login', fio: 'Corey');

        self::assertInstanceOf(CustomTransformUserDTO::class, $userDTO);

        self::assertFalse(isset($userDTO->login));
        self::assertFalse(isset($userDTO->fio));

        self::assertEquals('test-login', $userDTO->email);
        self::assertEquals('Corey', $userDTO->username);
    }
}
