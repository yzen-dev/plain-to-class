<?php

declare(strict_types=1);

namespace Tests\Integration;

use ReflectionException;
use ClassTransformer\Hydrator;
use PHPUnit\Framework\TestCase;
use Tests\Integration\DTO\UserNotTransformDTO;
use Tests\Integration\DTO\UserNotTransformRelationDTO;
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
        $model = (new Hydrator())->create(UserNotTransformDTO::class, $data);
        $this->assertTrue(true);
    }

}
