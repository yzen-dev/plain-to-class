<?php

namespace Tests\Units;

use PHPUnit\Framework\TestCase;
use Tests\Units\DTO\InstantiableClass;
use ClassTransformer\Reflection\ClassSchema;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\Exceptions\InstantiableClassException;

class ExceptionsTest extends TestCase
{

    public function testClassNotFoundException(): void
    {
        $this->expectException(ClassNotFoundException::class);
        new ClassSchema('TestClass');
    }

    public function testClassNotFoundExceptionMessage(): void
    {
        try {
            new ClassSchema('TestClass');
        } catch (ClassNotFoundException $exception) {
            $this->assertEquals('Class TestClass not found. Please check the class path you specified.', $exception->getMessage());
        }
    }

    public function testInstantiableClassException(): void
    {
        $this->expectException(InstantiableClassException::class);
        new ClassSchema(InstantiableClass::class);
    }

    public function testInstantiableClassMessage(): void
    {
        try {
            new ClassSchema(InstantiableClass::class);
        } catch (InstantiableClassException $exception) {
            $this->assertEquals('Class ' . InstantiableClass::class . ' is not instantiable.', $exception->getMessage());
        }
    }
}
