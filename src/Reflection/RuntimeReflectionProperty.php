<?php

declare(strict_types=1);

namespace ClassTransformer\Reflection;

use ClassTransformer\Attributes\FieldAlias;
use ClassTransformer\Attributes\NotTransform;
use ClassTransformer\Enums\TypeEnums;
use ClassTransformer\Reflection\Types\PropertyType;
use ClassTransformer\Reflection\Types\PropertyTypeFactory;
use ClassTransformer\TransformUtils;
use ReflectionAttribute;
use ReflectionNamedType;
use ReflectionProperty;
use function method_exists;

/**
 * Class GenericProperty
 */
final class RuntimeReflectionProperty implements \ClassTransformer\Contracts\ReflectionProperty
{
    /** @var ReflectionProperty */
    public ReflectionProperty $property;

    /** @var PropertyType */
    private PropertyType $type;

    /** @var class-string|string $propertyClass */
    public string $name;

    /** @var class-string */
    public string $class;


    /** @var array<class-string,array<string, array<ReflectionAttribute>>> */
    private static array $attributesCache = [];

    /**
     * @param ReflectionProperty $property
     */
    public function __construct(ReflectionProperty $property)
    {
        $this->property = $property;
        $this->class = $property->class;
        $this->name = $this->property->name;

        $this->type = PropertyTypeFactory::create($this);
    }

    public function getType(): PropertyType
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getDocComment(): string
    {
        $doc = $this->property->getDocComment();
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

        $attr = $this->property->getAttributes($name);
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
