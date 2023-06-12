<?php

namespace Tests\Units;

use ClassTransformer\Attributes\WritingStyle;
use ClassTransformer\CacheGenerator\CacheGenerator;
use ClassTransformer\ClassTransformerConfig;
use ClassTransformer\Reflection\CacheReflectionProperty;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use Tests\ClearCache;
use Tests\Units\DTO\ColorEnum;
use Tests\Units\DTO\UserCacheableDTO;

class CacheGeneratorTest extends TestCase
{
    use ClearCache;
    protected function setUp(): void
    {
        parent::setUp();

        if (
            !file_exists(ClassTransformerConfig::$cachePath) &&
            !mkdir($concurrentDirectory = ClassTransformerConfig::$cachePath, 0777, true) &&
            !is_dir($concurrentDirectory)
        ) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $class = str_replace('\\', '_', UserCacheableDTO::class);
        $path = __DIR__ . '/../../.cache' . DIRECTORY_SEPARATOR . $class . '.cache.php';

        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * @throws \ReflectionException
     */
    public function testGenerateException(): void
    {
        $this->expectException(RuntimeException::class);
        $class = new ReflectionClass(CacheGenerator::class);
        $method = $class->getMethod('makeCacheDir');
        $method->setAccessible(true);
        $method->invokeArgs(new CacheGenerator(UserCacheableDTO::class), [null]);
    }

    public function testGenerateCache(): void
    {
        $cacheGenerator = new CacheGenerator(UserCacheableDTO::class);

        $class = str_replace('\\', '_', UserCacheableDTO::class);
        $this->assertFileDoesNotExist(ClassTransformerConfig::$cachePath . DIRECTORY_SEPARATOR . $class . '.cache.php');

        $this->assertFalse($cacheGenerator->cacheExists());
        $cacheGenerator->generate();
        $this->assertFileExists(ClassTransformerConfig::$cachePath . DIRECTORY_SEPARATOR . $class . '.cache.php');
        $this->assertTrue($cacheGenerator->cacheExists());
        $cache = $cacheGenerator->get();
        $this->assertIsArray($cache);
        $this->assertArrayHasKey('properties', $cache);

        /** @var CacheReflectionProperty $property */
        $property = $cache['properties'][0];

        $this->assertEquals(UserCacheableDTO::class, $property->class);
        $this->assertEquals('id', $property->name);
        $this->assertEquals('int', $property->type);
        $this->assertTrue($property->isScalar);
        $this->assertFalse($property->hasSetMutator);
        $this->assertFalse($property->isArray);
        $this->assertFalse($property->isEnum);
        $this->assertFalse($property->notTransform);
        $this->assertEmpty($property->docComment);
        $this->assertIsArray($property->attributes);
        $this->assertEmpty($property->attributes);

        $property = $cache['properties'][5];
        $this->assertIsArray($property->attributes);
        $this->assertCount(1, $property->attributes);
        $this->assertIsArray($property->attributes[WritingStyle::class]);
        $this->assertEquals(WritingStyle::STYLE_CAMEL_CASE, $property->attributes[WritingStyle::class][0]);

        /** @var CacheReflectionProperty $property */
        $property = $cache['properties'][8];
        $this->assertIsString($property->getType());
        $this->assertEquals(ColorEnum::class, $property->getType());

        $cacheGenerator->generate();
    }

    protected function tearDown(): void
    {
        $this->clearCache();
    }
}
