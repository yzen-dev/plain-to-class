<?php

namespace Tests\Units;

use ClassTransformer\Enums\TypeEnums;
use ClassTransformer\Exceptions\ClassNotFoundException;
use RuntimeException;
use Tests\ClearCache;
use Tests\Units\DTO\ColorEnum;
use PHPUnit\Framework\TestCase;
use ClassTransformer\HydratorConfig;
use Tests\Units\DTO\UserCacheableDTO;
use ClassTransformer\Attributes\WritingStyle;
use ClassTransformer\Reflection\Types\EnumType;
use ClassTransformer\CacheGenerator\CacheGenerator;
use ClassTransformer\Reflection\CacheReflectionProperty;

class CacheGeneratorTest extends TestCase
{
    use ClearCache;

    private HydratorConfig $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = new HydratorConfig(true);
        if (
            !file_exists($this->config->cachePath) &&
            !mkdir($concurrentDirectory = $this->config->cachePath, 0777, true) &&
            !is_dir($concurrentDirectory)
        ) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $class = str_replace('\\', '_', UserCacheableDTO::class);
        $path = $this->config->cachePath . DIRECTORY_SEPARATOR . $class . '.cache.php';

        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * @throws \ReflectionException
     */
    public function testGenerateRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $dir = __DIR__ . '/CacheGeneratorTest.php';
        $generator = new CacheGenerator(UserCacheableDTO::class, new HydratorConfig(true, $dir));
        $dto = $generator->generate();
    }
    
    public function testGenerateClassNotFoundException(): void
    {
        $this->expectException(ClassNotFoundException::class);
        $generator = new CacheGenerator('FakeTestClass');
        $dto = $generator->generate();
    }

    public function testGenerateCache(): void
    {
        $cacheGenerator = new CacheGenerator(UserCacheableDTO::class, $this->config);

        $class = str_replace('\\', '_', UserCacheableDTO::class);
        $this->assertFileDoesNotExist($this->config->cachePath . DIRECTORY_SEPARATOR . $class . '.cache.php');

        $this->assertFalse($cacheGenerator->cacheExists());
        $cacheGenerator->generate();
        $this->assertFileExists($this->config->cachePath . DIRECTORY_SEPARATOR . $class . '.cache.php');
        $this->assertTrue($cacheGenerator->cacheExists());
        $cache = $cacheGenerator->get();
        $this->assertIsArray($cache);
        $this->assertArrayHasKey('properties', $cache);

        /** @var CacheReflectionProperty $property */
        $property = $cache['properties'][0];

        $this->assertEquals(UserCacheableDTO::class, $property->class);
        $this->assertEquals('id', $property->name);
        $this->assertEquals('int', $property->type->name);
        $this->assertFalse($property->hasSetMutator());
        $this->assertFalse($property->notTransform());
        $this->assertEmpty($property->getDocComment());

        $property = $cache['properties'][2];

        $this->assertEquals(UserCacheableDTO::class, $property->class);
        $this->assertEquals('phone', $property->name);
        $this->assertEquals(TypeEnums::TYPE_MIXED, $property->type->name);
        $this->assertCount(1, $property->getAliases());

        $property = $cache['properties'][5];
        $this->assertIsArray($property->attributes);
        $this->assertCount(1, $property->attributes);
        $this->assertIsArray($property->attributes[WritingStyle::class]);
        $this->assertEquals(WritingStyle::STYLE_CAMEL_CASE, $property->attributes[WritingStyle::class][0]);
        $this->assertIsArray($property->getAttribute(WritingStyle::class));
        $this->assertIsArray($property->getAttributeArguments(WritingStyle::class));

        /** @var CacheReflectionProperty $property */
        $property = $cache['properties'][8];
        $this->assertInstanceOf(EnumType::class, $property->type);
        $this->assertEquals(ColorEnum::class, $property->type->name);

        $cacheClass = $cacheGenerator->getClass();
        $this->assertEquals(UserCacheableDTO::class, $cacheClass->getClass());
        
    }

    protected function tearDown(): void
    {
        $this->clearCache();
    }
}
