<?php

declare(strict_types=1);

namespace Tests;

use ReflectionException;
use Tests\DTO\UserAfterTransformDTO;
use Tests\DTO\WritingStyleCamelCaseDTO;
use PHPUnit\Framework\TestCase;
use ClassTransformer\ClassTransformer;
use ClassTransformer\Exceptions\ClassNotFoundException;
use Tests\DTO\WritingStyleEmpyDTO;
use Tests\DTO\WritingStyleSnakeCaseDTO;

/**
 * Class AfterTransformTest
 * @package Tests
 */
class AfterTransformTest extends TestCase
{
    use FakerData;
    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testAfterTransformStyle(): void
    {
        $data = $this->getBaseObject();
        $userDTO = ClassTransformer::transform(UserAfterTransformDTO::class, $data);
        self::assertInstanceOf(UserAfterTransformDTO::class, $userDTO);
        self::assertEquals($data->id, $userDTO->id);
        self::assertEquals($data->email, $userDTO->email);
        self::assertEquals(777, $userDTO->balance);
    }
}
