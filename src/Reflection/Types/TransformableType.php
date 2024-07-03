<?php

declare(strict_types=1);

namespace ClassTransformer\Reflection\Types;

/**
 * Class TransformableType
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
final class TransformableType extends PropertyType
{
    /**
     * @param class-string|string $name Name of type
     * @param bool $isNullable
     */
    public function __construct(
        public string $name,
        public bool $isNullable
    ) {
        parent::__construct($this->name, false, $isNullable);
    }
}
