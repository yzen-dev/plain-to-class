<?php

namespace ClassTransformer\DTO;

use ReflectionType;
use ReflectionProperty;
use ReflectionNamedType;
use ReflectionUnionType;

/**
 * Class Property
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
class Property
{
    /**
     * @var ReflectionProperty
     */
    public ReflectionProperty $property;

    /**
     * @param ReflectionProperty $property
     */
    public function __construct(ReflectionProperty $property)
    {
        $this->property = $property;
    }

    /**
     * @return ReflectionType|null
     */
    public function getType()
    {
        return $this->property->getType();
    }

    /**
     * @return array<string>
     */
    public function getTypes(): array
    {
        $types = [];
        if ($this->getType() instanceof ReflectionUnionType) {
            $types = array_map(
                static function ($item) {
                    return $item->getName();
                },
                $this->getType()->getTypes()
            );
        }
        if ($this->getType() instanceof ReflectionNamedType) {
            $types = [$this->getType()->getName()];
        }

        if ($this->getType() !== null && $this->getType()->allowsNull()) {
            $types [] = 'null';
        }
        return $types;
    }

    /**
     * @return bool
     */
    public function isScalar(): bool
    {
        return count(array_intersect($this->getTypes(), ['int', 'float', 'double', 'string', 'bool', 'mixed'])) > 0;
    }

    /**
     * @return bool
     */
    public function isArray(): bool
    {
        return in_array('array', $this->getTypes(), true);
    }

    /**
     */
    public function getDocComment(): bool|string
    {
        return $this->property->getDocComment();
    }

    /**
     * @param string|null $name
     *
     * @return bool
     */
    public function existsAttribute(?string $name = null): bool
    {
        return $this->getAttributes($name) !== null;
    }

    /**
     * @param string|null $name
     *
     * @return null|array<mixed>
     */
    public function getAttributes(?string $name = null): ?array
    {
        $attr = $this->property->getAttributes($name);
        if (!empty($attr)) {
            return $attr;
        }
        return null;
    }
}
