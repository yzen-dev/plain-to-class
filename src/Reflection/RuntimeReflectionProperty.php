<?php

declare(strict_types=1);

namespace ClassTransformer\Reflection;

use ReflectionProperty;
use ReflectionAttribute;
use ClassTransformer\TransformUtils;
use ClassTransformer\Attributes\FieldAlias;
use ClassTransformer\Attributes\NotTransform;
use ClassTransformer\Reflection\Types\PropertyType;
use ClassTransformer\Reflection\Types\PropertyTypeFactory;

use function method_exists;

/**
 * Class GenericProperty
 */
final class RuntimeReflectionProperty extends \ClassTransformer\Contracts\ReflectionProperty
{
    /** @var ReflectionProperty */
    public ReflectionProperty $reflectionProperty;

    /** @var PropertyType */
    public PropertyType $type;


    /** @var array<class-string,array<string, array<ReflectionAttribute>>> */
    private static array $attributesCache = [];

    /**
     * @param ReflectionProperty $reflectionProperty
     */
    public function __construct(ReflectionProperty $reflectionProperty)
    {
        $this->reflectionProperty = $reflectionProperty;
        $this->class = $reflectionProperty->class;
        $this->name = $this->reflectionProperty->name;
        $this->type = PropertyTypeFactory::create($this);
    }

    /**
     * @return string
     */
    public function getDocComment(): string
    {
        $doc = $this->reflectionProperty->getDocComment();
        return $doc !== false ? $doc : '';
    }

    /**
     * @return bool
     */
    public function notTransform(): bool
    {
        return $this->getAttribute(NotTransform::class) !== null;
    }

    /**
     * @param string $name
     *
     * @template T
     * @return null|ReflectionAttribute
     */
    public function getAttribute(string $name): ?ReflectionAttribute
    {
        if (isset(self::$attributesCache[$this->class][$this->name][$name])) {
            return self::$attributesCache[$this->class][$this->name][$name];
        }

        $attr = $this->reflectionProperty->getAttributes($name);
        if (!empty($attr)) {
            return self::$attributesCache[$this->class][$this->name][$name] = $attr[0];
        }
        return null;
    }

    /**
     * @param string|null $name
     *
     * @return null|array<string>
     */
    public function getAttributeArguments(?string $name = null): ?array
    {
        return $this->getAttribute($name)?->getArguments();
    }

    /**
     * @return bool
     */
    public function hasSetMutator(): bool
    {
        return method_exists($this->class, TransformUtils::mutationSetterToCamelCase($this->name));
    }

    /**
     * @return array<string>
     */
    public function getAliases(): array
    {
        $aliases = $this->getAttributeArguments(FieldAlias::class);

        if (empty($aliases)) {
            return [];
        }

        $aliases = $aliases[0];

        if (is_string($aliases)) {
            $aliases = [$aliases];
        }
        return $aliases;
    }
}
