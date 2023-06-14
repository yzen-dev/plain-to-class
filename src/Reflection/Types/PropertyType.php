<?php

namespace ClassTransformer\Reflection\Types;

use ClassTransformer\Enums\TypeEnums;
use ReflectionNamedType;
use ReflectionType;

/**
 * Class PropertyType
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
class PropertyType
{
    public function __construct(
        private bool $nullable,
        private string $typeStr,
        private bool $isScalar
    ) {
    }

    public function isNullable()
    {
        return $this->nullable;
    }

    public function isScalar(): bool
    {
        return $this->isScalar;
    }

    public function getTypeStr()
    {
        return $this->typeStr;
    }
}
