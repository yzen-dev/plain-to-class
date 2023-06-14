<?php

namespace Tests\Units;

use ClassTransformer\Reflection\Types\EnumType;
use PHPUnit\Framework\TestCase;
use Tests\Units\DTO\ColorEnum;
use Tests\Units\DTO\ExtendedDto;
use ClassTransformer\Reflection\RuntimeReflectionProperty;

class RuntimeReflectionPropertyTest extends TestCase
{

    public function testCreatePropery(): void
    {
        $property = new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'email'));
        $this->assertEquals('string', $property->getType()->getTypeStr());
        $this->assertTrue($property->getType()->isScalar());
        $this->assertTrue($property->hasSetMutator());
        $this->assertEquals('email', $property->getName());

        $property = new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'color'));
        
        $this->assertInstanceOf(EnumType::class,$property->getType());
        $this->assertEquals(ColorEnum::class,$property->getType()->getTypeStr());
    }
}
