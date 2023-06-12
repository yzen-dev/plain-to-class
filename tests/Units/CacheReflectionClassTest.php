<?php

namespace Tests\Units;

use ClassTransformer\ClassTransformerConfig;
use ClassTransformer\Reflection\CacheReflectionClass;
use RuntimeException;
use PHPUnit\Framework\TestCase;
use Tests\ClearCache;
use Tests\Units\DTO\UserCacheableDTO;
use ClassTransformer\CacheGenerator\CacheGenerator;

class CacheReflectionClassTest extends TestCase
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
        $path = ClassTransformerConfig::$cachePath . DIRECTORY_SEPARATOR . $class . '.cache.php';

        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function testGetCache(): void
    {
        $reflection = new CacheReflectionClass(UserCacheableDTO::class);
        $reflectionProperties = $reflection->getProperties();

        $this->assertEquals(UserCacheableDTO::class, $reflectionProperties[0]->class);
        $this->assertEquals('id', $reflectionProperties[0]->name);
        $this->assertEquals('int', $reflectionProperties[0]->type);
        $this->assertTrue($reflectionProperties[0]->isScalar());
        $this->assertFalse($reflectionProperties[0]->hasSetMutator());
        $this->assertFalse($reflectionProperties[0]->isArray());
        $this->assertFalse($reflectionProperties[0]->isEnum());
        $this->assertFalse($reflectionProperties[0]->notTransform());
        $this->assertEmpty($reflectionProperties[0]->getDocComment());
        $this->assertEmpty($reflectionProperties[0]->getAttribute('addressThree'));
    }

    protected function tearDown(): void
    {
        $this->clearCache();
    }
}
