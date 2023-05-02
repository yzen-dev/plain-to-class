<?php

declare(strict_types=1);

namespace Tests\Integration;

use ClassTransformer\ClassTransformer;
use ClassTransformer\Exceptions\ClassNotFoundException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Tests\Integration\DTO\ColorEnum;
use Tests\Integration\DTO\ColorScalarEnum;
use Tests\Integration\DTO\ExampleWithEnumDTO;

/**
 * Class EnumTest
 *
 * @package Tests
 */
class EnumTest extends TestCase
{
    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testEmptyWritingStyle(): void
    {
        if (PHP_MAJOR_VERSION < 8 || (PHP_MAJOR_VERSION === 8 && PHP_MINOR_VERSION < 1)) {
            $this->markTestSkipped('Php version mismatch');
        }

        $data = [
            'colorEnum' => 'Red',
            'colorScalarEnum' => 'R',
        ];
        $model = ClassTransformer::transform(ExampleWithEnumDTO::class, $data);

        self::assertInstanceOf(ExampleWithEnumDTO::class, $model);
        self::assertInstanceOf(ColorEnum::class, $model->colorEnum);
        self::assertEquals(ColorEnum::Red, $model->colorEnum);
        self::assertInstanceOf(ColorScalarEnum::class, $model->colorScalarEnum);
        self::assertEquals(ColorScalarEnum::Red, $model->colorScalarEnum);
    }

}
