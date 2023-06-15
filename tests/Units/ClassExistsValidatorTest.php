<?php

namespace Tests\Units;

use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\Validators\ClassExistsValidator;
use ClassTransformer\ValueCasting;
use PHPUnit\Framework\TestCase;
use Tests\Units\DTO\ExtendedDto;
use ClassTransformer\Reflection\RuntimeReflectionProperty;

class ClassExistsValidatorTest extends TestCase
{

    public function testCreateProperty(): void
    {
        $this->expectException(ClassNotFoundException::class);
        new ClassExistsValidator('TestClass');
    }
}
