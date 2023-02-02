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
 * ./vendor/bin/phpbench run tests/Benchmark/CheckBench.php --report=default
 */
class CheckBench extends TestCase
{

    /**
     * @Revs(5000)
     */
    public function benchBaseReflection(): void
    {
        $data = $this->getPurcheseObject();

        $productOne = new ProductDTO();
        $productOne->id = $data['products'][0]['id'];
        $productOne->name = $data['products'][0]['name'];
        $productOne->price = $data['products'][0]['price'];

        $productTwo = new ProductDTO();
        $productTwo->id = $data['products'][0]['id'];
        $productTwo->name = $data['products'][0]['name'];
        $productTwo->price = $data['products'][0]['price'];

        $user = new UserDTO();
        $user->id = $data['user']['id'];
        $user->email = $data['user']['email'];
        $user->balance = $data['user']['balance'];

        $data = new PurchaseDTO();
        $data->products = [$productOne, $productTwo];
        $data->user = $user;
    }

    /**
     * @Revs(5000)
     */
    public function benchTransformReflection(): void
    {
        $data = $this->getPurcheseObject();
        ClassTransformer::transform(PurchaseDTO::class, $data);
    }

    public function getPurcheseObject(): array
    {
        return [
            'products' => [
                [
                    'id' => 1,
                    'name' => 'phone',
                    'price' => 43.03,
                ],
                [
                    'id' => 2,
                    'name' => 'bread',
                    'price' => 10.56,
                ]
            ],
            'user' => [
                'id' => 1,
                'email' => 'fake@mail.com',
                'balance' => 10012.23,
            ]
        ];
    }
}
