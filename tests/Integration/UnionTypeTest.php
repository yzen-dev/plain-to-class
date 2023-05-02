<?php

declare(strict_types=1);

namespace Tests\Integration;

use ReflectionException;
use PHPUnit\Framework\TestCase;
use ClassTransformer\ClassTransformer;
use Tests\Integration\DTO\UnionTypeDTO;
use ClassTransformer\Exceptions\ClassNotFoundException;

/**
 * Class UnionTypeTest
 * @package Tests
 */
class UnionTypeTest extends TestCase
{
    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testCustomTransform(): void
    {
        $data = [
            'id' => 1,
            'email' => 'test@mail.com',
            'balance' => 10.97,
        ];
        $userDTO = ClassTransformer::transform(UnionTypeDTO::class, $data);

        self::assertInstanceOf(UnionTypeDTO::class, $userDTO);

        self::assertEquals($data['id'], $userDTO->id);
        self::assertEquals($data['email'], $userDTO->email);
        self::assertEquals($data['balance'], $userDTO->balance);
    }

    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testCustomTransformUnion(): void
    {
        $data = [
            'id' => '1',
            'email' => 'test@mail.com',
            'balance' => '10.97',
        ];
        $userDTO = ClassTransformer::transform(UnionTypeDTO::class, $data);

        self::assertInstanceOf(UnionTypeDTO::class, $userDTO);

        self::assertEquals($data['id'], $userDTO->id);
        self::assertEquals($data['email'], $userDTO->email);
        self::assertEquals($data['balance'], $userDTO->balance);
    }

}
