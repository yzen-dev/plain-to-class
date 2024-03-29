<?php

declare(strict_types=1);

namespace Tests\Integration;

use ClassTransformer\Hydrator;
use ReflectionException;
use PHPUnit\Framework\TestCase;
use Tests\Integration\DTO\ConstructDto;
use Tests\Integration\DTO\EmptyClassDto;
use Tests\Integration\DTO\UserDTO;
use Tests\Integration\DTO\BasketDTO;
use Tests\Integration\DTO\ProductDTO;
use Tests\Integration\DTO\PurchaseDTO;
use ClassTransformer\ClassTransformer;
use Tests\Integration\DTO\ArrayScalarDTO;
use Tests\Integration\DTO\UserEmptyTypeDTO;
use ClassTransformer\Exceptions\ClassNotFoundException;

use function count;

/**
 * Class ClassTransformerTest
 *
 * @package Tests
 */
class ClassTransformerFromArrayTest extends TestCase
{
    use FakerData;

    /**
     * @throws ClassNotFoundException
     */
    public function testBaseArray(): void
    {
        $data = $this->getBaseArrayData();

        $userDTO = Hydrator::init()->create(UserDTO::class, $data);
        
        self::assertInstanceOf(UserDTO::class, $userDTO);
        self::assertEquals($data['id'], $userDTO->id);
        self::assertEquals($data['email'], $userDTO->email);
        self::assertEquals($data['balance'], $userDTO->balance);
        self::assertEquals($data['isBlocked'], $userDTO->isBlocked);
        self::assertIsInt($userDTO->id);
        self::assertIsString($userDTO->email);
        self::assertIsFloat($userDTO->balance);
        self::assertIsBool($userDTO->isBlocked);
    }
    
    /*public function testConstructFormatArray(): void
    {
        $data = $this->getBaseArrayData();
        $userDTO = ClassTransformer::transform(ConstructDto::class, $data);
        self::assertInstanceOf(UserDTO::class, $userDTO);
        self::assertEquals($data['id'], $userDTO->id);
        self::assertEquals($data['email'], $userDTO->email);
        self::assertEquals($data['balance'], $userDTO->balance);
        self::assertIsInt($userDTO->id);
        self::assertIsString($userDTO->email);
        self::assertIsFloat($userDTO->balance);
    }*/
    
    /**
     */
    public function testEmptyClass(): void
    {
        $data = $this->getBaseArrayData();
        $instance = Hydrator::init()->create(EmptyClassDto::class, $data);
        self::assertInstanceOf(EmptyClassDto::class, $instance);
    }

    /**
     * @throws ClassNotFoundException
     */
    public function testScalarArray(): void
    {
        $data = [
            'stringList' => [100, 200, 300],
            'intList' => [400, 500, 600]
        ];
        $dto = Hydrator::init()->create(ArrayScalarDTO::class, $data);
        self::assertInstanceOf(ArrayScalarDTO::class, $dto);
        self::assertIsString($dto->stringList[0]);
        self::assertEquals($dto->stringList[0], '100');
        self::assertIsInt($dto->intList[0]);
        self::assertEquals($dto->intList[0], 400);
    }

    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testNullArray(): void
    {
        $data = [
            'id' => 1,
            'products' => null
        ];

        $userDTO = Hydrator::init()->create(ArrayScalarDTO::class, $data);
        
        self::assertInstanceOf(ArrayScalarDTO::class, $userDTO);
    }

    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testTransformCollection(): void
    {
        $data = $this->getArrayUsers();
        
        $users = ClassTransformer::transformCollection(UserDTO::class, $data);
        self::assertCount(count($data), $users);
        
        $users = Hydrator::init()->createCollection(UserDTO::class, $data);

        self::assertCount(count($data), $users);
        foreach ($users as $key => $user) {
            self::assertInstanceOf(UserDTO::class, $user);
            self::assertEquals($data[$key]['id'], $user->id);
            self::assertEquals($data[$key]['email'], $user->email);
            self::assertEquals($data[$key]['balance'], $user->balance);
        }
    }

    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testTransformMultiple(): void
    {
        $userData = $this->getBaseArrayData();
        $purchaseData = $this->getRecursiveArrayData();

        $result = ClassTransformer::transformMultiple([UserDTO::class, PurchaseDTO::class], [$userData, $purchaseData]);
        [$user, $purchase] = $result;
        self::assertInstanceOf(UserDTO::class, $user);
        self::assertInstanceOf(PurchaseDTO::class, $purchase);
        
        $result = Hydrator::init()->createMultiple([UserDTO::class, PurchaseDTO::class], [$userData, $purchaseData]);

        [$user, $purchase] = $result;

        self::assertInstanceOf(UserDTO::class, $user);
        self::assertEquals($userData['id'], $user->id);
        self::assertEquals($userData['email'], $user->email);
        self::assertEquals($userData['balance'], $user->balance);

        self::assertInstanceOf(PurchaseDTO::class, $purchase);
    }

    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testRecursiveArray(): void
    {
        $data = $this->getRecursiveArrayData();
        $purchaseDTO = Hydrator::init()->create(PurchaseDTO::class, $data);
        
        self::assertInstanceOf(PurchaseDTO::class, $purchaseDTO);
        self::assertInstanceOf(UserDTO::class, $purchaseDTO->user);
        self::assertEquals($data['user']['id'], $purchaseDTO->user->id);
        self::assertEquals($data['user']['email'], $purchaseDTO->user->email);
        self::assertEquals($data['user']['balance'], $purchaseDTO->user->balance);
        self::assertIsInt($purchaseDTO->user->id);
        self::assertIsString($purchaseDTO->user->email);
        self::assertIsFloat($purchaseDTO->user->balance);
        foreach ($purchaseDTO->products as $key => $product) {
            self::assertInstanceOf(ProductDTO::class, $product);
            self::assertEquals($data['products'][$key]['id'], $product->id);
            self::assertEquals($data['products'][$key]['name'], $product->name);
            self::assertEquals($data['products'][$key]['price'], $product->price);
            self::assertIsInt($product->id);
            self::assertIsString($product->name);
            self::assertIsFloat($product->price);
        }
    }


    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testTripleRecursiveArray(): void
    {
        $data = $this->getTripleRecursiveArray();
        
        $basketDTO = Hydrator::init()->create(BasketDTO::class, $data);
        
        foreach ($basketDTO->orders as $key => $purchase) {
            self::assertInstanceOf(PurchaseDTO::class, $purchase);
            self::assertInstanceOf(UserDTO::class, $purchase->user);
            self::assertEquals($data['orders'][$key]['user']['id'], $purchase->user->id);
            self::assertEquals($data['orders'][$key]['user']['email'], $purchase->user->email);
            self::assertEquals($data['orders'][$key]['user']['balance'], $purchase->user->balance);
            self::assertIsInt($purchase->user->id);
            self::assertIsString($purchase->user->email);
            self::assertIsFloat($purchase->user->balance);
            foreach ($purchase->products as $productKey => $product) {
                self::assertInstanceOf(ProductDTO::class, $product);
                self::assertEquals($data['orders'][$key]['products'][$productKey]['id'], $product->id);
                self::assertEquals($data['orders'][$key]['products'][$productKey]['name'], $product->name);
                self::assertEquals($data['orders'][$key]['products'][$productKey]['price'], $product->price);
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
        $data = $this->getBaseArrayData();
        
        $userDTO = Hydrator::init()->create(UserEmptyTypeDTO::class, $data);
        
        self::assertInstanceOf(UserEmptyTypeDTO::class, $userDTO);
        self::assertEquals($data['id'], $userDTO->id);
        self::assertEquals($data['email'], $userDTO->email);
        self::assertEquals($data['balance'], $userDTO->balance);
    }
}
