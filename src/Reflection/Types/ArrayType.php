<?php

declare(strict_types=1);

namespace ClassTransformer\Reflection\Types;

/**
 * Class ArrayType
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
class ArrayType extends PropertyType
{
    /** @var string|class-string */
    public string $itemsType;
    public bool $isScalarItems;
}
