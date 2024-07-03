<?php

declare(strict_types=1);

namespace Tests\Integration;

use ReflectionException;
use ClassTransformer\Hydrator;
use PHPUnit\Framework\TestCase;
use Tests\Integration\DTO\UserDTO;
use Tests\Integration\DTO\BasketDTO;
use Tests\Integration\DTO\ProductDTO;
use Tests\Integration\DTO\PurchaseDTO;
use Tests\Integration\DTO\Full\PurchaseDTO as FullPurchaseDto;
use Tests\Integration\DTO\EmptyClassDto;
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

        $userDTO = (new Hydrator())->create(UserDTO::class, $data);
        
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
        $userDTO = (new Hydrator())->create(ConstructDto::class, $data);
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
        $instance = (new Hydrator())->create(EmptyClassDto::class, $data);
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
        $dto = (new Hydrator())->create(ArrayScalarDTO::class, $data);
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

        $userDTO = (new Hydrator())->create(ArrayScalarDTO::class, $data);
        
        self::assertInstanceOf(ArrayScalarDTO::class, $userDTO);
    }

    /**
     * @throws ReflectionException|ClassNotFoundException
     */
    public function testTransformCollection(): void
    {
        $data = $this->getArrayUsers();
        
        $users = (new Hydrator())->createCollection(UserDTO::class, $data);
        self::assertCount(count($data), $users);
        
        $users = (new Hydrator())->createCollection(UserDTO::class, $data);

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

        $result = (new Hydrator())->createMultiple([UserDTO::class, PurchaseDTO::class], [$userData, $purchaseData]);
        [$user, $purchase] = $result;
        self::assertInstanceOf(UserDTO::class, $user);
        self::assertInstanceOf(PurchaseDTO::class, $purchase);
        
        $result = (new Hydrator())->createMultiple([UserDTO::class, PurchaseDTO::class], [$userData, $purchaseData]);

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
        $purchaseDTO = (new Hydrator())->create(PurchaseDTO::class, $data);
        
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
        
        $basketDTO = (new Hydrator())->create(BasketDTO::class, $data);
        
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
    
    public function testEmptyTypeObject(): void
    {
        $data = $this->getBaseArrayData();
        
        $userDTO = (new Hydrator())->create(UserEmptyTypeDTO::class, $data);
        
        self::assertInstanceOf(UserEmptyTypeDTO::class, $userDTO);
        self::assertEquals($data['id'], $userDTO->id);
        self::assertEquals($data['email'], $userDTO->email);
        self::assertEquals($data['balance'], $userDTO->balance);
    }

    public function testFull(): void
    {
        $data = $this->getPurcheseObject();
        $object = (new Hydrator)->create(FullPurchaseDto::class, $data);
        $this->assertEquals($data['user']['id'], $object->user->id);
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
                'createdAt' => '2023-04-10',
            ],
            'createdAt' => '2023-04-10',
            'address' => $this->getAddress()
        ];
    }

    private function getAddress()
    {
        return [
            "source" => "мск сухонска 11/-89",
            "result" => "г Москва, ул Сухонская, д 11, кв 89",
            "postal_code" => "127642",
            "country" => "Россия",
            "country_iso_code" => "RU",
            "federal_district" => "Центральный",
            "region_fias_id" => "0c5b2444-70a0-4932-980c-b4dc0d3f02b5",
            "region_kladr_id" => "7700000000000",
            "region_iso_code" => "RU-MOW",
            "region_with_type" => "г Москва",
            "region_type" => "г",
            "region_type_full" => "город",
            "region" => "Москва",
            "area_fias_id" => null,
            "area_kladr_id" => null,
            "area_with_type" => null,
            "area_type" => null,
            "area_type_full" => null,
            "area" => null,
            "city_fias_id" => null,
            "city_kladr_id" => null,
            "city_with_type" => null,
            "city_type" => null,
            "city_type_full" => null,
            "city" => null,
            "city_area" => "Северо-восточный",
            "city_district_fias_id" => null,
            "city_district_kladr_id" => null,
            "city_district_with_type" => "р-н Северное Медведково",
            "city_district_type" => "р-н",
            "city_district_type_full" => "район",
            "city_district" => "Северное Медведково",
            "settlement_fias_id" => null,
            "settlement_kladr_id" => null,
            "settlement_with_type" => null,
            "settlement_type" => null,
            "settlement_type_full" => null,
            "settlement" => null,
            "street_fias_id" => "95dbf7fb-0dd4-4a04-8100-4f6c847564b5",
            "street_kladr_id" => "77000000000283600",
            "street_with_type" => "ул Сухонская",
            "street_type" => "ул",
            "street_type_full" => "улица",
            "street" => "Сухонская",
            "house_fias_id" => "5ee84ac0-eb9a-4b42-b814-2f5f7c27c255",
            "house_kladr_id" => "7700000000028360004",
            "house_type" => "д",
            "house_type_full" => "дом",
            "house" => "11",
            "block_type" => null,
            "block_type_full" => null,
            "block" => null,
            "flat_fias_id" => "f26b876b-6857-4951-b060-ec6559f04a9a",
            "flat_type" => "кв",
            "flat_type_full" => "квартира",
            "flat" => "89",
            "flat_area" => "34.6",
            "square_meter_price" => "239953",
            "flat_price" => "8302374",
            "postal_box" => null,
            "fias_id" => "f26b876b-6857-4951-b060-ec6559f04a9a",
            "fias_code" => "77000000000000028360004",
            "fias_level" => "9",
            "kladr_id" => "7700000000028360004",
            "capital_marker" => "0",
            "okato" => "45280583000",
            "oktmo" => "45362000",
            "tax_office" => "7715",
            "tax_office_legal" => "7715",
            "timezone" => "UTC+3",
            "geo_lat" => "55.8782557",
            "geo_lon" => "37.65372",
            "beltway_hit" => "IN_MKAD",
            "beltway_distance" => null,
            "qc_geo" => 0,
            "qc_complete" => 0,
            "qc_house" => 2,
            "qc" => 0,
            "unparsed_parts" => null,
            "metro" => [
                [
                    "distance" => 1.1,
                    "line" => "Калужско-Рижская",
                    "name" => "Бабушкинская"
                ],
                [
                    "distance" => 1.2,
                    "line" => "Калужско-Рижская",
                    "name" => "Медведково"
                ],
                [
                    "distance" => 2.5,
                    "line" => "Калужско-Рижская",
                    "name" => "Свиблово"
                ]
            ]
        ];
    }
}
