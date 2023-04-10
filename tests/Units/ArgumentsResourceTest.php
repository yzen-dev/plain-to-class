<?php

namespace Tests\Units;

use ClassTransformer\ArgumentsResource;
use ClassTransformer\Exceptions\ValueNotFoundException;
use ClassTransformer\Reflection\RuntimeReflectionProperty;
use PHPUnit\Framework\TestCase;
use Tests\Units\DTO\UserDTO;

class ArgumentsResourceTest extends TestCase
{
    public function testBaseKey(): void
    {
        $data = ['id' => 1];
        $resource = new ArgumentsResource($data);
        $value = $resource->getValue(
            new RuntimeReflectionProperty(new \ReflectionProperty(UserDTO::class, 'id'))
        );
        $this->assertEquals($data['id'], $value);
    }
    
    public function testBaseValueNotFoundException(): void
    {
        $this->expectException(ValueNotFoundException::class);
        $resource = new ArgumentsResource(['test' => 1]);
        $resource->getValue(
            new RuntimeReflectionProperty(new \ReflectionProperty(UserDTO::class, 'id'))
        );
    }

    public function testEmptyWritingStyle(): void
    {
        $this->expectException(ValueNotFoundException::class);
        $data = ['id' => 1];
        $resource = new ArgumentsResource($data);
        $value = $resource->getValue(
            new RuntimeReflectionProperty(new \ReflectionProperty(UserDTO::class, 'addressOne'))
        );
        $this->assertEquals($data['id'], $value);
    }

    public function testCamelCaseWritingStyleKey(): void
    {
        $data = ['addressTwo' => 'test'];
        $resource = new ArgumentsResource($data);
        $value = $resource->getValue(
            new RuntimeReflectionProperty(new \ReflectionProperty(UserDTO::class, 'address_two'))
        );
        $this->assertEquals($data['addressTwo'], $value);
    }

    public function testSnakeCaseWritingStyleKey(): void
    {
        $data = ['address_three' => 'test'];
        $resource = new ArgumentsResource($data);
        $value = $resource->getValue(
            new RuntimeReflectionProperty(new \ReflectionProperty(UserDTO::class, 'addressThree'))
        );
        $this->assertEquals($data['address_three'], $value);
    }

    public function testFinalValueNotFoundException(): void
    {
        $this->expectException(ValueNotFoundException::class);
        $resource = new ArgumentsResource(['test' => 1]);
        $resource->getValue(
            new RuntimeReflectionProperty(new \ReflectionProperty(UserDTO::class, 'balance'))
        );
    }
}
