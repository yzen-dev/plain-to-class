<?php

namespace Tests\Benchmark;

use ReflectionNamedType;
use PHPUnit\Framework\TestCase;
use ClassTransformer\ClassTransformerConfig;
use ClassTransformer\Reflection\CacheReflectionProperty;
use ClassTransformer\Reflection\RuntimeReflectionProperty;
use Tests\Benchmark\DTO\UserDto;

/**
 * Class CheckBench
 *
 * @package Tests\Benchmark
 *
 * ./vendor/bin/phpbench run tests/Benchmark/SerializeVsArrayBench.php --report=default
 */
class SerializeVsArrayBench extends TestCase
{


    /**
     * @Revs(10000)
     */
    public function benchSerializeReflection(): void
    {
        $class = str_replace('\\', '_', UserDto::class);
        $path = ClassTransformerConfig::$cachePath . DIRECTORY_SEPARATOR . $class . '.cache.php';

        if (file_exists($path)) {
            unlink($path);
        }

        $cache = [
            'properties' => []
        ];

        $refInstance = new \ReflectionClass(UserDto::class);

        $properties = $refInstance->getProperties();

        foreach ($properties as $item) {
            $property = new RuntimeReflectionProperty($item);
            if ($property->type instanceof ReflectionNamedType) {
                $type = $property->type->getName();
            } else {
                $type = $property->type;
            }
            $attrs = $property->property->getAttributes();
            $attributes = [];
            if (!empty($attrs)) {
                foreach ($attrs as $attr) {
                    $attributes[$attr->getName()] = $attr->getArguments();
                }
            }
            $cache['properties'][] = new CacheReflectionProperty(
                $property->class,
                $property->name,
                $type,
                $property->types,
                $property->isScalar,
                $property->hasSetMutator(),
                $property->isArray(),
                $property->isEnum(),
                $property->notTransform(),
                $property->transformable(),
                $property->getDocComment(),
                $attributes,
            );
        }
        
        file_put_contents(
            $path,
            serialize($cache)
        );
        
    }
    public function benchArrayReflection(): void
    {
        $class = str_replace('\\', '_', UserDto::class);
        $path = ClassTransformerConfig::$cachePath . DIRECTORY_SEPARATOR . $class . '.cache-array.php';

        if (file_exists($path)) {
            unlink($path);
        }

        $cache = [
            'properties' => []
        ];

        $refInstance = new \ReflectionClass(UserDto::class);

        $properties = $refInstance->getProperties();

        foreach ($properties as $item) {
            $property = new RuntimeReflectionProperty($item);
            if ($property->type instanceof ReflectionNamedType) {
                $type = $property->type->getName();
            } else {
                $type = $property->type;
            }
            $attrs = $property->property->getAttributes();
            $attributes = [];
            if (!empty($attrs)) {
                foreach ($attrs as $attr) {
                    $attributes[$attr->getName()] = $attr->getArguments();
                }
            }
            $cache['properties'][] = [
                'class' => $property->class,
                'name' => $property->name,
                'type' => $type,
                'types' => $property->types,
                'isScalar' => $property->isScalar,
                'hasSetMutator' => $property->hasSetMutator(),
                'isArray' => $property->isArray(),
                'isEnum' => $property->isEnum(),
                'notTransform' => $property->notTransform(),
                'transformable' => $property->transformable(),
                'docComment' => $property->getDocComment(),
                'attributes' => $attributes,
            ];
        }

        file_put_contents(
            $path,
            '<?php ' . PHP_EOL . 'return ' . var_export($cache, true) . ';'
        );
        
    }
}
