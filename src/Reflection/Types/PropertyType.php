<?php

declare(strict_types=1);

namespace ClassTransformer\Reflection\Types;

/**
 * Class PropertyType
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
class PropertyType
{
    /**
     * @param string $name Name of type
     * @param bool $isScalar
     */
    public function __construct(
        public string $name,
        public bool $isScalar
    ) {
    }
}
