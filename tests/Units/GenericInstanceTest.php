<?php

declare(strict_types=1);

namespace Tests\Units;

use ClassTransformer\GenericInstance;
use ReflectionException;
use Tests\DTO\UserAfterTransformDTO;
use Tests\DTO\WritingStyleCamelCaseDTO;
use PHPUnit\Framework\TestCase;
use ClassTransformer\ClassTransformer;
use ClassTransformer\Exceptions\ClassNotFoundException;
use Tests\DTO\WritingStyleEmpyDTO;
use Tests\DTO\WritingStyleSnakeCaseDTO;
use Tests\FakerData;

/**
 * Class AfterTransformTest
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
        new GenericInstance('Test\Fake\Class');
    }
}
