<?php

namespace Tests\Units;

use ClassTransformer\Exceptions\ClassNotFoundException;
use PHPUnit\Framework\TestCase;
use Tests\Integration\DTO\PurchaseDTO;
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
    
    public function testGetClass(): void
    {
        $instance = new RuntimeReflectionClass(PurchaseDTO::class);
        $this->assertEquals(PurchaseDTO::class, $instance->getClass());
    }
    
    public function testGenerateClassNotFoundException(): void
    {
        $this->expectException(ClassNotFoundException::class);
        $instance = new RuntimeReflectionClass('FakeTestClass');
        $instance->getProperties();
    }
}
