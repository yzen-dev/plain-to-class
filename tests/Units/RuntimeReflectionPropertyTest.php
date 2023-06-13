<?php

namespace Tests\Units;

use PHPUnit\Framework\TestCase;
use Tests\Units\DTO\ColorEnum;
use Tests\Units\DTO\ExtendedDto;
use ClassTransformer\Reflection\RuntimeReflectionProperty;

class RuntimeReflectionPropertyTest extends TestCase
{

    public function testCreatePropery(): void
    {
        $property = new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'email'));
        $this->assertEquals('string', $property->getType());
        $this->assertTrue($property->isScalar());
        $this->assertTrue($property->hasSetMutator());
        $this->assertFalse($property->isEnum());
        $this->assertEquals('email', $property->getName());

        $property = new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'color'));
        $this->assertTrue($property->isEnum());
        $this->assertEquals(ColorEnum::class, $property->getType());
    }
}
