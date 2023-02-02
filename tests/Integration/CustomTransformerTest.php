<?php

declare(strict_types=1);

namespace Tests\Integration;

use ClassTransformer\ClassTransformer;
use ClassTransformer\Exceptions\ClassNotFoundException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Tests\Integration\DTO\CustomTransformUserDTO;
use Tests\Integration\DTO\CustomTransformUserDTOArray;
use Tests\Integration\DTO\UserNoTypeArrayDTO;

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
    public function testArrayNotType(): void
    {
        $data = [
            'id' => 1,
            'products' => [
                ['id' => 1, 'price' => 43.03,],
                ['id' => 2, 'price' => 10.56,],
            ],
        ];
        $userDTO = ClassTransformer::transform(UserNoTypeArrayDTO::class, $data);

        self::assertInstanceOf(UserNoTypeArrayDTO::class, $userDTO);

        self::assertTrue(isset($userDTO->id));
        self::assertTrue(isset($userDTO->products));
        self::assertEquals($data['id'], $userDTO->id);

        foreach ($userDTO->products as $key => $product) {
            self::assertEquals($data['products'][$key]['id'], $product['id']);
            self::assertEquals($data['products'][$key]['price'], $product['price']);
        }
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
