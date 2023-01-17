<?php

declare(strict_types=1);

namespace Tests;

use ReflectionException;
use Tests\DTO\ColorEnum;
use Tests\DTO\ColorScalarEnum;
use Tests\DTO\ExampleWithEnumDTO;
use Tests\DTO\WritingStyleCamelCaseDTO;
use PHPUnit\Framework\TestCase;
use ClassTransformer\ClassTransformer;
use ClassTransformer\Exceptions\ClassNotFoundException;
use Tests\DTO\WritingStyleEmpyDTO;
use Tests\DTO\WritingStyleSnakeCaseDTO;

/**
 * Class EnumTest
 * @package Tests
 */
class EnumTest extends TestCase
{
    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testEmptyWritingStyle(): void
    {
        $data = [
            'colorEnum' => 'Red',
            'colorScalarEnum' => 'Red',
        ];
        $model = ClassTransformer::transform(ExampleWithEnumDTO::class, $data);
        
        self::assertInstanceOf(ExampleWithEnumDTO::class, $model);
        self::assertInstanceOf(ColorEnum::class, $model->colorEnum);
        self::assertEquals(ColorEnum::Red, $model->colorEnum);
        self::assertInstanceOf(ColorScalarEnum::class, $model->colorScalarEnum);
        self::assertEquals(ColorScalarEnum::Red, $model->colorScalarEnum);
    }

}
