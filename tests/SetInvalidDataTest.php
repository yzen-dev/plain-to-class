<?php

declare(strict_types=1);

namespace Tests;

use ClassTransformer\Exceptions\InvalidArgumentException;
use Tests\DTO\UserDTO;
use ReflectionException;
use PHPUnit\Framework\TestCase;
use ClassTransformer\ClassTransformer;

/**
 * Class SetInvalidDataTest
 *
 * @package Tests
 */
class SetInvalidDataTest extends TestCase
{
    /**
     * @throws ReflectionException|InvalidArgumentException
     */
    /*public function testBaseArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $data = [
            'id' => 1,
            'email' => 7,
            'balance' => 128.41
        ];
        ClassTransformer::transform(UserDTO::class, $data);
    }*/

}
