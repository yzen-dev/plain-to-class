<?php

declare(strict_types=1);

namespace Tests;

use ReflectionException;
use Tests\DTO\WritingStyleCamelCaseDTO;
use PHPUnit\Framework\TestCase;
use ClassTransformer\ClassTransformer;
use ClassTransformer\Exceptions\ClassNotFoundException;
use Tests\DTO\WritingStyleSnakeCaseDTO;

/**
 * Class UnionTypeTest
 * @package Tests
 */
class WritingStyleTest extends TestCase
{
    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testSnakeCaseTransform(): void
    {
        $data = [
            'contact_fio' => 'corey',
            'contact_email' => 'test@mail.com',
        ];
        $model = ClassTransformer::transform(WritingStyleCamelCaseDTO::class, $data);

        self::assertInstanceOf(WritingStyleCamelCaseDTO::class, $model);

        self::assertEquals($data['contact_fio'], $model->contactFio);
        self::assertEquals($data['contact_email'], $model->contactEmail);
    }

    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testCamelCaseTransform(): void
    {
        $data = [
            'contactFio' => 'corey',
            'contactEmail' => 'test@mail.com',
        ];
        $model = ClassTransformer::transform(WritingStyleSnakeCaseDTO::class, $data);

        self::assertInstanceOf(WritingStyleSnakeCaseDTO::class, $model);

        self::assertEquals($data['contactFio'], $model->contact_fio);
        self::assertEquals($data['contactEmail'], $model->contact_email);
    }


}
