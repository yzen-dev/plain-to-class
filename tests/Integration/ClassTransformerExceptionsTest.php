<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use ClassTransformer\ClassTransformer;
use Tests\Integration\DTO\FakeClassDTO;
use ClassTransformer\Exceptions\ClassNotFoundException;

/**
 * Class ClassTransformerExceptionsTest
 *
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
}
