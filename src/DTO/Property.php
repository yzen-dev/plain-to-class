<?php

namespace ClassTransformer\DTO;

use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

/**
 * Class Property
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
class Property
{
    public ReflectionProperty $property;

    public function __construct(ReflectionProperty $property)
    {
        $this->property = $property;
    }

    public function getType()
    {
        return $this->property->getType();
    }

    /**
     * @param ReflectionType|null $propertyType
     *
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
        
        if ($this->getType()->allowsNull()) {
            $types [] = 'null';
        }
        return $types;
    }

    /**
     * @return bool
     */
    public function isScalar(): bool
    {
        return count(array_intersect($this->getTypes(), [ 'int', 'float', 'double', 'string', 'bool', 'mixed'])) > 0;
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
}
