<?php

namespace Tests\Benchmark;

use ClassTransformer\ClassTransformer;
use ClassTransformer\ClassTransformerConfig;
use PHPUnit\Framework\TestCase;
use Tests\Benchmark\DTO\AddressDto;
use Tests\Benchmark\DTO\ProductDto;
use Tests\Benchmark\DTO\PurchaseDto;
use Tests\Benchmark\DTO\UserDto;
use Tests\Benchmark\DTO\UserTypeEnum;

/**
 * Class CheckBench
 *
 * @package Tests\Benchmark
 *
 * ./vendor/bin/phpbench run tests/Benchmark/FullCheckBench.php --report=default
 */
class FullCheckBench extends TestCase
{

    /**
     * @Revs(10000)
     */
    public function benchBaseReflection(): void
    {
        $data = $this->getPurcheseObject();

        $purchase = new PurchaseDto();

        $user = new UserDto();
        $user->id = $data['user']['id'];
        $user->email = $data['user']['contact'];
        $user->balance = $data['user']['balance'];
        $user->real_address = $data['user']['real_address'] ?? $data['user']['realAddress'];
        $user->type = UserTypeEnum::from($data['user']['type']);
        $purchase->user = $user;

        $address = new AddressDto();
        $address->city = $data['address']['city'];
        $address->street = $data['address']['street'];
        $address->house = $data['address']['house'];

        $purchase->address = $address;

        $purchase->createdAt = new \DateTime($data['createdAt']);

        foreach ($data['products'] as $product) {
            $newProduct = new ProductDto();
            $newProduct->id = $product['id'];
            $newProduct->name = $product['name'];
            $newProduct->price = $product['price'];
            $purchase->products [] = $newProduct;
        }
    }

    /**
     * @Revs(10000)
     */
    public function benchTransformReflection(): void
    {
        $data = $this->getPurcheseObject();
        ClassTransformer::transform(PurchaseDto::class, $data);
    }

    /**
     * @Revs(10000)
     */
    public function benchTransformCacheReflection(): void
    {
        ClassTransformerConfig::$cache = true;
        $data = $this->getPurcheseObject();
        ClassTransformer::transform(PurchaseDto::class, $data);
    }

    public function getPurcheseObject(): array
    {
        return [
            'createdAt' => '2023-04-10 12:30:23',
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
                    'id' => 3,
                    'name' => 'book',
                    'price' => 5.5,
                    'description' => 'test description for book',
                    'count' => 333
                ],
                [
                    'id' => 4,
                    'name' => 'PC',
                    'price' => 100,
                    'description' => 'test description for PC',
                    'count' => 7
                ]
            ],
            'user' => [
                'id' => 1,
                'contact' => 'fake@mail.com',
                'balance' => 10012.23,
                'type' => 'admin',
                'realAddress' => 'test address',
            ],
            'address' => [
                'city' => 'NY',
                'street' => 'street',
                'house' => '14',
            ]
        ];
    }
}
