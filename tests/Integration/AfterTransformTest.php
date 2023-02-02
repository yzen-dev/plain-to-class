<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use ClassTransformer\ClassTransformer;
use Tests\Integration\DTO\UserAfterTransformDTO;
use ClassTransformer\Exceptions\ClassNotFoundException;

/**
 * Class AfterTransformTest
 * @package Tests
 */
class AfterTransformTest extends TestCase
{
    use FakerData;
    /**
     * @throws ClassNotFoundException
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
