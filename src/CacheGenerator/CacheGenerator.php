<?php

namespace ClassTransformer\CacheGenerator;

use RuntimeException;
use ReflectionException;
use ReflectionNamedType;
use ReflectionClass as PhpReflectionClass;
use ClassTransformer\Reflection\RuntimeReflectionProperty;

/**
 * Class CacheGenerator
 *
 * @template TClass
 */
class CacheGenerator
{
    /** @param class-string $class */
    private string $class;

    /**
     * @param class-string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
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
     * @return array
     * @throws ReflectionException
     */
    public function generate(): array
    {

        if (
            !file_exists(__DIR__ . '/../../.cache') &&
            !mkdir($concurrentDirectory = __DIR__ . '/../../.cache', 0777, true) &&
            !is_dir($concurrentDirectory)
        ) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $class = str_replace('\\', '_', $this->class);
        $path = __DIR__ . '/../../.cache/' . $class . '.cache.php';

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
        return require __DIR__ . '/../../.cache/' . $class . '.cache.php';
    }
}
