<?php

declare(strict_types=1);

namespace Tests\Units;

use ClassTransformer\ArgumentsResource;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\GenericInstance;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Tests\Integration\FakerData;

/**
 * Class AfterTransformTest
 *
 * @package Tests
 */
class GenericInstanceTest extends TestCase
{
    use FakerData;

    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testAfterTransformStyle(): void
    {
        $this->expectException(ClassNotFoundException::class);
        new GenericInstance('Test\Fake\Class', new ArgumentsResource());
    }
}
