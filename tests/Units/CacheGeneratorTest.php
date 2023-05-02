<?php

namespace Tests\Units;

use ClassTransformer\CacheGenerator\CacheGenerator;
use ClassTransformer\ClassTransformerConfig;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use Tests\Units\DTO\UserCacheableDTO;

class CacheGeneratorTest extends TestCase
{
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
        $path = __DIR__ . '/../../.cache' . '/' . $class . '.cache.php';

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
        $this->assertFileDoesNotExist(ClassTransformerConfig::$cachePath . '/' . $class . '.cache.php');

        $this->assertFalse($cacheGenerator->cacheExists());
        $cacheGenerator->generate();
        $this->assertFileExists(ClassTransformerConfig::$cachePath . '/' . $class . '.cache.php');
        $cache = $cacheGenerator->get();

        $this->assertIsArray($cache);
        $this->assertArrayHasKey('properties', $cache);

        $this->assertEquals(UserCacheableDTO::class, $cache['properties'][0]['class']);
        $this->assertEquals('id', $cache['properties'][0]['name']);
        $this->assertEquals('int', $cache['properties'][0]['type']);
        $this->assertTrue($cache['properties'][0]['isScalar']);
        $this->assertFalse($cache['properties'][0]['hasSetMutator']);
        $this->assertFalse($cache['properties'][0]['isArray']);
        $this->assertFalse($cache['properties'][0]['isEnum']);
        $this->assertFalse($cache['properties'][0]['notTransform']);
        $this->assertFalse($cache['properties'][0]['docComment']);

        $cacheGenerator->generate();
    }
}
