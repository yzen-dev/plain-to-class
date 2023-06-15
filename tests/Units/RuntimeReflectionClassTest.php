<?php

namespace Tests\Units;

use PHPUnit\Framework\TestCase;
use Tests\Units\DTO\AbstractClass;
use ClassTransformer\Reflection\RuntimeReflectionClass;
use ClassTransformer\Exceptions\InstantiableClassException;

class RuntimeReflectionClassTest extends TestCase
{

    public function testInstantiable(): void
    {
        $this->expectException(InstantiableClassException::class);
        $instance = new RuntimeReflectionClass(AbstractClass::class);
        $instance->getProperties();
    }
}
