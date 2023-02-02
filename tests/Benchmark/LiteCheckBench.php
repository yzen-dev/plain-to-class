<?php

namespace Tests\Benchmark;

use PHPUnit\Framework\TestCase;
use Tests\Benchmark\DTO\UserDTO;
use Tests\Benchmark\DTO\ProductDTO;
use Tests\Benchmark\DTO\PurchaseDTO;
use ClassTransformer\ClassTransformer;

/**
 * Class CheckBench
 *
 * @package Tests\Benchmark
 *
 * ./vendor/bin/phpbench run tests/Benchmark/LiteCheckBench.php --report=default
 */
class LiteCheckBench extends TestCase
{

    /**
     * @Revs(5000)
     */
    public function benchBaseReflection(): void
    {
        $data = $this->getPurcheseObject();

        $productOne = new ProductDTO();
        $productOne->id = $data['id'];
        $productOne->name = $data['name'];
        $productOne->price = $data['price'];
    }

    /**
     * @Revs(5000)
     */
    public function benchTransformReflection(): void
    {
        $data = $this->getPurcheseObject();
        ClassTransformer::transform(ProductDTO::class, $data);
    }

    public function getPurcheseObject(): array
    {
        return [
            'id' => 1,
            'name' => 'phone',
            'price' => 43.03,
        ];
    }
}
