<?php

namespace Tests\Units;

use ClassTransformer\Reflection\Types\ArrayType;
use ClassTransformer\Reflection\Types\EnumType;
use ClassTransformer\Reflection\Types\PropertyType;
use ClassTransformer\Reflection\Types\PropertyTypeFactory;
use ClassTransformer\Reflection\Types\ScalarType;
use ClassTransformer\Reflection\Types\TransformableType;
use PHPUnit\Framework\TestCase;
use Tests\Units\DTO\ExtendedDto;
use ClassTransformer\Reflection\RuntimeReflectionProperty;

class PropertyTypeFactoryTest extends TestCase
{

    public function testCreateProperty(): void
    {
        $type = PropertyTypeFactory::create(new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'id')));
        $this->assertInstanceOf(ScalarType::class, $type);

        $type = PropertyTypeFactory::create(new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'color')));
        $this->assertInstanceOf(EnumType::class, $type);

        $type = PropertyTypeFactory::create(new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'stringItems')));
        $this->assertInstanceOf(ArrayType::class, $type);

        $type = PropertyTypeFactory::create(new RuntimeReflectionProperty(new \ReflectionProperty(ExtendedDto::class, 'user')));
        $this->assertInstanceOf(TransformableType::class, $type);
    }
}
