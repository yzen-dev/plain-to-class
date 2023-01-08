<?php

namespace Tests\Benchmark;

use Tests\DTO\ColorEnum;
use PHPUnit\Framework\TestCase;

/**
 * Class BaseBench
 *
 * @package Tests\Benchmark
 */
class GetEnumBench extends TestCase
{
    /**
     * @Revs(5000)
     */
    public function benchConstant(): void
    {
        $key = 'Red';
        $value = constant(ColorEnum::class . "::{$key}");
    }

    /**
     * @Revs(5000)
     */
    public function benchReflection(): void
    {
        $key = 'Red';
        $reflection = new \ReflectionEnum(ColorEnum::class);
        if ($reflection->hasConstant($key)) {
            $value = $reflection->getConstant($key);
        }
    }

}
