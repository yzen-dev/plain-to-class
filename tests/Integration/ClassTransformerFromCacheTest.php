<?php

declare(strict_types=1);

namespace Tests\Integration;

use ClassTransformer\Hydrator;
use ClassTransformer\HydratorConfig;
use PHPUnit\Framework\TestCase;
use Tests\ClearCache;
use Tests\Integration\DTO\UserDTO;
use Tests\Integration\DTO\ProductDTO;
use Tests\Integration\DTO\PurchaseDTO;
use ClassTransformer\Exceptions\ClassNotFoundException;

/**
 * Class ClassTransformerTest
 *
 * @package Tests
 */
class ClassTransformerFromCacheTest extends TestCase
{
    use FakerData, ClearCache;

    /**
     * @throws ClassNotFoundException
     */
    public function testRecursiveObject(): void
    {
        $data = $this->getRecursiveObject();
        $data->orders = $this->getArrayUsers();
        
        $purchaseDTO = (new Hydrator(new HydratorConfig(true)))
            ->create(PurchaseDto::class, $data);

        self::assertInstanceOf(PurchaseDTO::class, $purchaseDTO);
        self::assertInstanceOf(UserDTO::class, $purchaseDTO->user);
        self::assertEquals($data->user->id, $purchaseDTO->user->id);
        self::assertEquals($data->user->email, $purchaseDTO->user->email);
        self::assertEquals($data->user->balance, $purchaseDTO->user->balance);
        self::assertIsInt($purchaseDTO->user->id);
        self::assertIsString($purchaseDTO->user->email);
        self::assertIsFloat($purchaseDTO->user->balance);

        foreach ($purchaseDTO->products as $key => $product) {
            self::assertInstanceOf(ProductDTO::class, $product);
            self::assertEquals($data->products[$key]->id, $product->id);
            self::assertEquals($data->products[$key]->name, $product->name);
            self::assertEquals($data->products[$key]->price, $product->price);
            self::assertIsInt($product->id);
            self::assertIsString($product->name);
            self::assertIsFloat($product->price);
        }
    }

    protected function tearDown(): void
    {
        $this->clearCache();
    }

}
