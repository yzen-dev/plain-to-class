<?php

namespace Tests\Units;

use ClassTransformer\ArgumentsRepository;
use ClassTransformer\Exceptions\ValueNotFoundException;
use ClassTransformer\Reflection\RuntimeReflectionProperty;
use PHPUnit\Framework\TestCase;
use Tests\Units\DTO\UserDTO;

class ArgumentsResourceTest extends TestCase
{
    public function testBaseKey(): void
    {
        $data = ['id' => 1];
        $resource = new ArgumentsRepository($data);
        $value = $resource->getValue(
            new RuntimeReflectionProperty(new \ReflectionProperty(UserDTO::class, 'id'))
        );
        $this->assertEquals($data['id'], $value);
    }

    public function testBaseValueNotFoundException(): void
    {
        $this->expectException(ValueNotFoundException::class);
        $resource = new ArgumentsRepository(['test' => 1]);
        $resource->getValue(
            new RuntimeReflectionProperty(new \ReflectionProperty(UserDTO::class, 'id'))
        );
    }

    public function testEmptyWritingStyle(): void
    {
        $this->expectException(ValueNotFoundException::class);
        $data = ['id' => 1];
        $resource = new ArgumentsRepository($data);
        $value = $resource->getValue(
            new RuntimeReflectionProperty(new \ReflectionProperty(UserDTO::class, 'addressOne'))
        );
        $this->assertEquals($data['id'], $value);
    }

    public function testCamelCaseWritingStyleKey(): void
    {
        $data = ['addressTwo' => 'test'];
        $resource = new ArgumentsRepository($data);
        $value = $resource->getValue(
            new RuntimeReflectionProperty(new \ReflectionProperty(UserDTO::class, 'address_two'))
        );
        $this->assertEquals($data['addressTwo'], $value);

        $data = ['test_case' => 'test2'];

        $this->expectException(ValueNotFoundException::class);
        $resource2 = new ArgumentsRepository($data);

        $resource2->getValue(
            new RuntimeReflectionProperty(new \ReflectionProperty(UserDTO::class, 'testCase'))
        );
    }

    public function testSnakeCaseWritingStyleKey(): void
    {
        $data = ['address_three' => 'test'];
        $resource = new ArgumentsRepository($data);
        $value = $resource->getValue(
            new RuntimeReflectionProperty(new \ReflectionProperty(UserDTO::class, 'addressThree'))
        );
        $this->assertEquals($data['address_three'], $value);

        $data = ['testCase' => 'test2'];

        $this->expectException(ValueNotFoundException::class);
        $resource2 = new ArgumentsRepository($data);

        $resource2->getValue(
            new RuntimeReflectionProperty(new \ReflectionProperty(UserDTO::class, 'test_case'))
        );
    }

    public function testFinalValueNotFoundException(): void
    {
        $this->expectException(ValueNotFoundException::class);
        $resource = new ArgumentsRepository(['test' => 1]);
        $resource->getValue(
            new RuntimeReflectionProperty(new \ReflectionProperty(UserDTO::class, 'balance'))
        );
    }
}
