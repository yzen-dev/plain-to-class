<?php

namespace Tests\Benchmark;

use ClassTransformer\ClassTransformer;
use PHPUnit\Framework\TestCase;
use Tests\Benchmark\DTO\ProductDTO;
use Tests\Benchmark\DTO\PurchaseDTO;
use Tests\Benchmark\DTO\UserDTO;
use Tests\Benchmark\DTO\UserTypeEnum;

/**
 * Class CheckBench
 *
 * @package Tests\Benchmark
 *
 * ./vendor/bin/phpbench run tests/Benchmark/CheckBench.php --report=default
 */
class FullCheckBench extends TestCase
{

    /**
     * @Revs(5000)
     * @Iterations(5)
     */
    public function benchBaseReflection(): void
    {
        $data = $this->getPurcheseObject();

        $purchase = new PurchaseDTO();

        $user = new UserDTO();
        $user->id = $data['user']['id'];
        $user->email = $data['user']['contact'];
        $user->balance = $data['user']['balance'];
        $user->real_address = $data['user']['real_address'] ?? $data['user']['realAddress'];
        $user->type = UserTypeEnum::from($data['user']['type']);
        $purchase->user = $user;

        foreach ($data['products'] as $product) {
            $newProduct = new ProductDTO();
            $newProduct->id = $product['id'];
            $newProduct->name = $product['name'];
            $newProduct->price = $product['price'];
            $purchase->products []= $newProduct;
        }
    }

    /**
     * @Revs(5000)
     * @Iterations(5)
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
                    'description' => 'test description for phone',
                    'count' => 123
                ],
                [
                    'id' => 2,
                    'name' => 'bread',
                    'price' => 10.56,
                    'description' => 'test description for bread',
                    'count' => 321
                ],
                [
                    'id' => 2,
                    'name' => 'book',
                    'price' => 5.5,
                    'description' => 'test description for book',
                    'count' => 333
                ]
            ],
            'user' => [
                'id' => 1,
                'contact' => 'fake@mail.com',
                'balance' => 10012.23,
                'type' => 'admin',
                'realAddress' => 'test address',
            ]
        ];
    }
}
