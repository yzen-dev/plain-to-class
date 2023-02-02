<?php

declare(strict_types=1);

namespace Tests\Integration;

use ClassTransformer\ClassTransformer;
use ClassTransformer\Exceptions\ClassNotFoundException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Tests\Integration\DTO\UserNotTransformDTO;
use Tests\Integration\DTO\UserNotTransformRelationDTO;

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
