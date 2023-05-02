<?php

namespace ClassTransformer\CacheGenerator;

use ClassTransformer\ClassTransformerConfig;
use RuntimeException;
use ReflectionException;
use ReflectionNamedType;
use ReflectionClass as PhpReflectionClass;
use ClassTransformer\Reflection\RuntimeReflectionProperty;

/**
 * Class CacheGenerator
 *
 * @template TClass
 * @psalm-api
 * @psalm-immutable
 */
class CacheGenerator
{
    /** @psalm-param class-string<TClass> $class */
    private string $class;

    /**
     * @param class-string<TClass> $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function generate(): array
    {
        $this->makeCacheDir(ClassTransformerConfig::$cachePath);
        $class = str_replace('\\', '_', $this->class);
        $path = ClassTransformerConfig::$cachePath . '/' . $class . '.cache.php';

        if (file_exists($path)) {
            unlink($path);
        }

        $cache = [
            'properties' => []
        ];
        
        $refInstance = new PhpReflectionClass($this->class);

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
        return $cache;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $class = str_replace('\\', '_', $this->class);
        return require ClassTransformerConfig::$cachePath . '/' . $class . '.cache.php';
    }

    /**
     * @return bool
     */
    public function cacheExists(): bool
    {
        $class = str_replace('\\', '_', $this->class);
        return file_exists('./.cache/' . $class . '.cache.php');
    }


    /**
     * @param string|null $path
     *
     * @return void
     */
    private function makeCacheDir(?string $path): void
    {
        $concurrentDirectory = ClassTransformerConfig::$cachePath;
        if (
            empty($path) ||
            (
                !file_exists($concurrentDirectory) &&
                !mkdir($concurrentDirectory, 0777, true) &&
                !is_dir($concurrentDirectory)
            )
        ) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }
}
