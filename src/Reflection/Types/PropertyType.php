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
    public function __construct(
        public string $name,
        public bool $isScalar,
        public bool $nullable,
    ) {
    }
}
