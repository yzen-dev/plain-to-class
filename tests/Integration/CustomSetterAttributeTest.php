<?php

declare(strict_types=1);

namespace Tests\Integration;

use ClassTransformer\ClassTransformer;
use ClassTransformer\Exceptions\ClassNotFoundException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Tests\Integration\DTO\CustomSetterAttibuteDTO;

/**
 * Class CustomSetterAttributeTest
 * @package Tests
 */
class CustomSetterAttributeTest extends TestCase
{
    use FakerData;
    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testCustomSetterAttibute(): void
    {
        $data = [
            'id' => 77,
            'real_address' => 'TEST ADDRESS',
            'userName' => 'yzen',
        ];
        $userDTO = ClassTransformer::transform(CustomSetterAttibuteDTO::class, $data);
        self::assertInstanceOf(CustomSetterAttibuteDTO::class, $userDTO);
        self::assertEquals($data['id'], $userDTO->id);
        self::assertEquals(strtolower($data['real_address']), $userDTO->real_address);
        self::assertEquals(strtoupper($data['userName']), $userDTO->userName);
    }
}
