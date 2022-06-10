<?php

declare(strict_types=1);

namespace Tests;

use ReflectionException;
use PHPUnit\Framework\TestCase;
use Tests\DTO\UserNotTransformDTO;
use ClassTransformer\ClassTransformer;
use Tests\DTO\UserNotTransformRelationDTO;
use ClassTransformer\Exceptions\ClassNotFoundException;

/**
 * Class UnionTypeTest
 *
 * @package Tests
 */
class NotTransformTest extends TestCase
{
    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testNotTransform(): void
    {
        $data = [
            'fio' => 'corey',
            'relation' => new UserNotTransformRelationDTO(),
        ];
        $model = ClassTransformer::transform(UserNotTransformDTO::class, $data);
        $this->assertTrue(true);
    }

}
