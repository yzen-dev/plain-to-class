<?php

namespace ClassTransformer\Reflection\Types;

use ClassTransformer\Enums\TypeEnums;

/**
 * Class ArrayType
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
class ArrayType extends PropertyType
{
    public string $itemsType;
    public bool $isScalarItems;

    /**
     * @return string
     */
    public function getItemsType(): string
    {
        return $this->itemsType;
    }

    /**
     * @return bool
     */
    public function isScalarItems(): bool
    {
        return $this->isScalarItems;
    }
}
