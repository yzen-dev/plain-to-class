<?php

declare(strict_types=1);

namespace ClassTransformer\Reflection\Types;

/**
 * Class ArrayType
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
final class ArrayType extends PropertyType
{
    /**
     * @param string|class-string $name Name of type
     * @param bool $isScalar
     * @param bool $isNullable
     * @param string|class-string $itemsType
     * @param bool $isScalarItems
     */
    public function __construct(
        public string $name,
        public bool $isScalar,
        public bool $isNullable,
        public string $itemsType,
        public bool $isScalarItems,
    ) {
    }
}
