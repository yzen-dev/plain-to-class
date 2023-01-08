<?php

namespace Tests\Benchmark;

use Tests\DTO\ProductDTO;
use Tests\DTO\UserDTO;
use Tests\FakerData;
use Tests\DTO\PurchaseDTO;
use PHPUnit\Framework\TestCase;
use ClassTransformer\ClassTransformer;

/**
 * Class CheckBench
 *
 * @package Tests\Benchmark
 */
class CheckBench extends TestCase
{
    use FakerData;

    /**
     * @Revs(5000)
     */
    public function benchBaseReflection(): void
    {
        $data = $this->getRecursiveObject();
        $productOne = new ProductDTO();
        $productOne->id = 1;
        $productOne->name = 'phone';
        $productOne->price = 43.03;

        $productTwo = new ProductDTO();
        $productTwo->id = 2;
        $productTwo->name = 'bread';
        $productTwo->price = 10.56;

        $user = new UserDTO();
        $user->id = 1;
        $user->email = 'fake@mail.com';
        $user->balance = 10012.23;

        $data = new PurchaseDTO();
        $data->products = [$productOne, $productTwo];
        $data->user = $user;
    }

    /**
     * @Revs(5000)
     */
    public function benchTransformReflection(): void
    {
        $data = $this->getRecursiveObject();
        $purchaseDTO = ClassTransformer::transform(PurchaseDTO::class, $data);
    }

}
