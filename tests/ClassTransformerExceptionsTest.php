<?php

declare(strict_types=1);

namespace Tests;

use ClassTransformer\Exceptions\ClassNotFoundException;
use Tests\DTO\FakeClassDTO;
use PHPUnit\Framework\TestCase;
use ClassTransformer\ClassTransformer;
use Tests\DTO\UserDTO;

/**
 * Class ClassTransformerExceptionsTest
 * @package Tests
 */
class ClassTransformerExceptionsTest extends TestCase
{
    use FakerData;


    public function testClassNotFound(): void
    {
        $this->expectException(ClassNotFoundException::class);
        ClassTransformer::transform(FakeClassDTO::class, ['fake' => ['exception']]);
    }

    public function testClassNotFoundPhp8(): void
    {
        $this->expectException(ClassNotFoundException::class);

        ClassTransformer::transform(FakeClassDTO::class, fake: ['exception']);
    }

    
    public function testInvalidExtractArray(): void
    {
        $this->expectException(\RuntimeException::class);
        $userDTO = ClassTransformer::transform([UserDTO::class], a: 1);
        self::assertInstanceOf(UserDTO::class, $userDTO);
        self::assertTrue(!isset($userDTO->id));
    }
}
