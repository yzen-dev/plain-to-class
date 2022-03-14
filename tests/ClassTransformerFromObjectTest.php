<?php

declare(strict_types=1);

namespace Tests;

use Tests\DTO\UserDTO;
use ReflectionException;
use Tests\DTO\BasketDTO;
use Tests\DTO\ProductDTO;
use Tests\DTO\PurchaseDTO;
use PHPUnit\Framework\TestCase;
use Tests\DTO\UserEmptyTypeDTO;
use ClassTransformer\ClassTransformer;
use ClassTransformer\Exceptions\ClassNotFoundException;

/**
 * Class ClassTransformerTest
 *
 * @package Tests
 */
class ClassTransformerFromObjectTest extends TestCase
{
    use FakerData;

    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testEmptyData(): void
    {
        $userDTO = ClassTransformer::transform(UserDTO::class);
        self::assertInstanceOf(UserDTO::class, $userDTO);
        self::assertTrue(!isset($userDTO->id));
    }

    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testBaseObject(): void
    {
        $data = $this->getBaseObject();
        $userDTO = ClassTransformer::transform(UserDTO::class, $data);
        self::assertInstanceOf(UserDTO::class, $userDTO);
        self::assertEquals($data->id, $userDTO->id);
        self::assertEquals($data->email, $userDTO->email);
        self::assertEquals($data->balance, $userDTO->balance);
        self::assertIsInt($userDTO->id);
        self::assertIsString($userDTO->email);
        self::assertIsFloat($userDTO->balance);
    }

    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testBaseObjectPhp8(): void
    {
        $data = $this->getBaseObject();
        $userDTO = ClassTransformer::transform(UserDTO::class, id: $data->id, email: $data->email, balance: $data->balance);
        self::assertInstanceOf(UserDTO::class, $userDTO);
        self::assertEquals($data->id, $userDTO->id);
        self::assertEquals($data->email, $userDTO->email);
        self::assertEquals($data->balance, $userDTO->balance);
        self::assertIsInt($userDTO->id);
        self::assertIsString($userDTO->email);
        self::assertIsFloat($userDTO->balance);
    }

    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testMixedType(): void
    {
        $data = [
            'id'=>1,
            'email'=>'test',
            'balance'=>1,
            'mixed'=> ['1'],
        ];
        $userDTO = ClassTransformer::transform(UserDTO::class,$data);
        self::assertInstanceOf(UserDTO::class, $userDTO);
    }

    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testRecursiveObject(): void
    {
        $data = $this->getRecursiveObject();
        $purchaseDTO = ClassTransformer::transform(PurchaseDTO::class, $data);
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

    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testTripleRecursiveObject(): void
    {
        $data = $this->getTripleRecursiveObject();
        $basketDTO = ClassTransformer::transform(BasketDTO::class, $data);
        foreach ($basketDTO->orders as $key => $purchase) {
            self::assertInstanceOf(PurchaseDTO::class, $purchase);
            self::assertInstanceOf(UserDTO::class, $purchase->user);
            self::assertEquals($data->orders[$key]->user->id, $purchase->user->id);
            self::assertEquals($data->orders[$key]->user->email, $purchase->user->email);
            self::assertEquals($data->orders[$key]->user->balance, $purchase->user->balance);
            self::assertIsInt($purchase->user->id);
            self::assertIsString($purchase->user->email);
            self::assertIsFloat($purchase->user->balance);
            foreach ($purchase->products as $productKey => $product) {
                self::assertInstanceOf(ProductDTO::class, $product);
                self::assertEquals($data->orders[$key]->products[$productKey]->id, $product->id);
                self::assertEquals($data->orders[$key]->products[$productKey]->name, $product->name);
                self::assertEquals($data->orders[$key]->products[$productKey]->price, $product->price);
                self::assertIsInt($product->id);
                self::assertIsString($product->name);
                self::assertIsFloat($product->price);
            }
        }
    }

    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testEmptyTypeObject(): void
    {
        $data = $this->getBaseObject();
        $userDTO = ClassTransformer::transform(UserEmptyTypeDTO::class, $data);
        self::assertInstanceOf(UserEmptyTypeDTO::class, $userDTO);
        self::assertEquals($data->id, $userDTO->id);
        self::assertEquals($data->email, $userDTO->email);
        self::assertEquals($data->balance, $userDTO->balance);
    }
}
