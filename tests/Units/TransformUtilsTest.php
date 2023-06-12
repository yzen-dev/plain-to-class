<?php

namespace Tests\Units;

use ClassTransformer\TransformUtils;
use PHPUnit\Framework\TestCase;

class TransformUtilsTest extends TestCase
{

    public function testAttributeToSnakeCase(): void
    {
        $snakeCase = TransformUtils::attributeToSnakeCase('exampleCamelCase');
        $this->assertEquals('example_camel_case', $snakeCase);
        // test for cache
        $snakeCase = TransformUtils::attributeToSnakeCase('exampleCamelCase');
        $this->assertEquals('example_camel_case', $snakeCase);
    }

    public function testAttributeToCamelCase(): void
    {
        $camelCase = TransformUtils::attributeToCamelCase('example_snake_case');
        $this->assertEquals('exampleSnakeCase', $camelCase);
        // test for cache
        $camelCase = TransformUtils::attributeToCamelCase('example_snake_case');
        $this->assertEquals('exampleSnakeCase', $camelCase);
    }

    public function testMutationSetterToCamelCase(): void
    {
        $setter = TransformUtils::mutationSetterToCamelCase('example');
        $this->assertEquals('setExampleAttribute', $setter);
        // test for cache
        $setter = TransformUtils::mutationSetterToCamelCase('example');
        $this->assertEquals('setExampleAttribute', $setter);
    }

    public function testGetClassFromPhpDoc(): void
    {
        $type = TransformUtils::getClassFromPhpDoc('/** ' . PHP_EOL . '* @var array<\Tests\Integration\DTO\PurchaseDTO> $orders Order list ' . PHP_EOL . ' */');
        $this->assertEquals('\Tests\Integration\DTO\PurchaseDTO', $type);

        $type = TransformUtils::getClassFromPhpDoc('/** @var array<> $orders Order list */');
        $this->assertNull($type);
    }
}
