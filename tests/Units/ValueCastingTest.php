<?php

namespace Tests\Units;

use ClassTransformer\Exceptions\InvalidArgumentException;
use ClassTransformer\ValueCasting;
use PHPUnit\Framework\TestCase;
use Tests\Units\DTO\ExtendedDto;
use ClassTransformer\Reflection\RuntimeReflectionProperty;
use Tests\Units\DTO\TypesDto;

class ValueCastingTest extends TestCase
{

    public function testCreateNotValidProperty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $caster = new ValueCasting(
            new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'id'))
        );
        $caster->castAttribute([1,2]);
    }
    
    public function testCreateProperty(): void
    {
        $caster = new ValueCasting(
            new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'color'))
        );
        $value = $caster->castAttribute(2);
        $this->assertEquals(2, $value);


        $caster = new ValueCasting(
            new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'isBlocked'))
        );
        $value = $caster->castAttribute(0);
        $this->assertIsBool($value);
        $this->assertFalse($value);

        $caster = new ValueCasting(
            new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'email'))
        );
        $value = $caster->castAttribute(101010);
        $this->assertIsString($value);
        $this->assertEquals('101010', $value);

        $caster = new ValueCasting(
            new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'balance'))
        );
        $value = $caster->castAttribute('12');
        $this->assertIsFloat($value);
        $this->assertEquals(12.00, $value);

        $caster = new ValueCasting(
            new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'id'))
        );
        $value = $caster->castAttribute('1');
        $this->assertIsInt($value);
        $this->assertEquals(1, $value);
        
        
        $caster = new ValueCasting(new RuntimeReflectionProperty(new \ReflectionProperty(TypesDto::class, 'nullableInt')));
        $value = $caster->castAttribute(null);
        $this->assertNull($value);

        $caster = new ValueCasting(new RuntimeReflectionProperty(new \ReflectionProperty(TypesDto::class, 'nullableString')));
        $value = $caster->castAttribute(null);
        $this->assertNull($value);
        
        $caster = new ValueCasting(new RuntimeReflectionProperty(new \ReflectionProperty(TypesDto::class, 'emptyString')));
        $value = $caster->castAttribute('');
        $this->assertNull($value);

        $caster = new ValueCasting(new RuntimeReflectionProperty(new \ReflectionProperty(TypesDto::class, 'nullableFloat')));
        $value = $caster->castAttribute(null);
        $this->assertNull($value);

        $caster = new ValueCasting(new RuntimeReflectionProperty(new \ReflectionProperty(TypesDto::class, 'nullableBool')));
        $value = $caster->castAttribute(null);
        $this->assertNull($value);
        
    }

    public function testCreateArrayProperty(): void
    {

        $caster = new ValueCasting(
            new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'intItems'))
        );
        $value = $caster->castAttribute('1');
        $this->assertIsString($value);
        $this->assertEquals('1', $value);

        $caster = new ValueCasting(
            new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'intItems'))
        );
        $value = $caster->castAttribute(['1']);
        $this->assertIsInt($value[0]);
        $this->assertEquals(1, $value[0]);

        $caster = new ValueCasting(
            new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'floatItems'))
        );
        $value = $caster->castAttribute(['10']);
        $this->assertIsFloat($value[0]);
        $this->assertEquals(10.0, $value[0]);

        $caster = new ValueCasting(
            new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'stringItems'))
        );
        $value = $caster->castAttribute([10]);
        $this->assertIsString($value[0]);
        $this->assertEquals('10', $value[0]);

        $caster = new ValueCasting(
            new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'boolItems'))
        );
        $value = $caster->castAttribute([0]);
        $this->assertIsBool($value[0]);
        $this->assertFalse($value[0]);

        $caster = new ValueCasting(
            new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'booleanItems'))
        );
        $value = $caster->castAttribute([0]);
        $this->assertIsBool($value[0]);
        $this->assertFalse($value[0]);

        $caster = new ValueCasting(
            new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'mixedItems'))
        );
        $value = $caster->castAttribute(['10']);
        $this->assertIsString($value[0]);
        $this->assertEquals('10', $value[0]);
    }
}
