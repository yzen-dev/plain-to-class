<?php

declare(strict_types=1);

namespace Tests\Integration;

use ReflectionException;
use ClassTransformer\Hydrator;
use PHPUnit\Framework\TestCase;
use Tests\Integration\DTO\UserConstructorProperties;
use Tests\Integration\DTO\UserNoTypeArrayDTO;
use Tests\Integration\DTO\CustomTransformUserDTO;
use Tests\Integration\DTO\CustomTransformUserDTOArray;
use ClassTransformer\Exceptions\ClassNotFoundException;

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
        $userDTO = (new Hydrator())->create(CustomTransformUserDTOArray::class, $data);

        self::assertInstanceOf(CustomTransformUserDTOArray::class, $userDTO);

        self::assertFalse(isset($userDTO->login));
        self::assertFalse(isset($userDTO->fio));

        self::assertEquals('test-login', $userDTO->email);
        self::assertEquals('Corey', $userDTO->username);
    }
    
    public function testConstructProperties(): void
    {
        $data = new \stdClass();
        $data->id = 1;
        $data->email = 'fake@mail.com';
        $data->balance = 128.43;
        $data->isBlocked = false;
        
        $userDTO = (new Hydrator())->create(UserConstructorProperties::class, $data);

        self::assertInstanceOf(UserConstructorProperties::class, $userDTO);
        self::assertEquals($data->email, $userDTO->email);
        self::assertEquals($data->balance, $userDTO->balance);
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
        $userDTO = (new Hydrator())->create(UserNoTypeArrayDTO::class, $data);

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
        $userDTO = (new Hydrator())->create(CustomTransformUserDTO::class, login: 'test-login', fio: 'Corey');

        self::assertInstanceOf(CustomTransformUserDTO::class, $userDTO);

        self::assertFalse(isset($userDTO->login));
        self::assertFalse(isset($userDTO->fio));

        self::assertEquals('test-login', $userDTO->email);
        self::assertEquals('Corey', $userDTO->username);
    }
}
