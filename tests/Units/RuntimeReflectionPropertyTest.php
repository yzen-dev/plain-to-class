<?php

namespace Tests\Units;

use Tests\Units\DTO\ColorEnum;
use PHPUnit\Framework\TestCase;
use Tests\Units\DTO\ExtendedDto;
use ClassTransformer\Reflection\ClassProperty;
use ClassTransformer\Reflection\Types\EnumType;

class RuntimeReflectionPropertyTest extends TestCase
{

    public function testCreatePropery(): void
    {
        $property = new ClassProperty(new \ReflectionProperty(ExtendedDto::class, 'email'));
        $this->assertEquals('string', $property->type->name);
        $this->assertTrue($property->type->isScalar);
        $this->assertTrue($property->hasSetMutator());
        $this->assertEquals('email', $property->name);

        $property = new ClassProperty(new \ReflectionProperty(ExtendedDto::class, 'color'));

        $this->assertInstanceOf(EnumType::class, $property->type);
        $this->assertEquals(ColorEnum::class, $property->type->name);
    }
}
