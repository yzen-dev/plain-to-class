<?php

namespace Tests\Benchmark\Bid;

use ClassTransformer\Hydrator;
use PHPUnit\Framework\TestCase;
use ClassTransformer\HydratorConfig;
use Tests\Benchmark\Bid\Dto\Address\AddressClean;
use Tests\Benchmark\Bid\Dto\Address\MetroDto;
use Tests\Benchmark\Bid\Dto\UserDto;
use Tests\Benchmark\Bid\Dto\ProductDto;
use Tests\Benchmark\Bid\Dto\PurchaseDto;
use Tests\Benchmark\Bid\Dto\UserTypeEnum;

/**
 * Class CheckBench
 *
 * @package Tests\Benchmark
 *
 * ./vendor/bin/phpbench run tests/Benchmark/Bid/ManyCheckBench.php --report=default
 */
class ManyCheckBench extends TestCase
{

    public function __construct()
    {
        parent::__construct('bench');
    }

    /**
     * @Revs(10000)
     */
    public function benchBaseReflection(): void
    {
        $data = $this->getPurcheseObject();

        for ($i = 0; $i < 10; ++$i) {
            $purchase = new PurchaseDto();

            $user = new UserDto();
            $user->id = $data['user']['id'];
            $user->email = $data['user']['email'] ?? $data['user']['contact'];
            $user->balance = $data['user']['balance'];
            $user->real_address = $data['user']['real_address'] ?? $data['user']['realAddress'];
            $user->type = UserTypeEnum::from($data['user']['type']);
            $user->createdAt = new \DateTime($data['user']['createdAt']);
            $purchase->user = $user;

            $address = new AddressClean();
            $address->source = $data['address']['source'];
            $address->result = $data['address']['result'];
            $address->postal_code = $data['address']['postal_code'];
            $address->country = $data['address']['country'];
            $address->country_iso_code = $data['address']['country_iso_code'];
            $address->federal_district = $data['address']['federal_district'];
            $address->region_fias_id = $data['address']['region_fias_id'];
            $address->region_kladr_id = $data['address']['region_kladr_id'];
            $address->region_iso_code = $data['address']['region_iso_code'];
            $address->region_with_type = $data['address']['region_with_type'];
            $address->region_type = $data['address']['region_type'];
            $address->region_type_full = $data['address']['region_type_full'];
            $address->region = $data['address']['region'];
            $address->area_fias_id = $data['address']['area_fias_id'];
            $address->area_kladr_id = $data['address']['area_kladr_id'];
            $address->area_with_type = $data['address']['area_with_type'];
            $address->area_type = $data['address']['area_type'];
            $address->area_type_full = $data['address']['area_type_full'];
            $address->area = $data['address']['area'];
            $address->city_fias_id = $data['address']['city_fias_id'];
            $address->city_kladr_id = $data['address']['city_kladr_id'];
            $address->city_with_type = $data['address']['city_with_type'];
            $address->city_type = $data['address']['city_type'];
            $address->city_type_full = $data['address']['city_type_full'];
            $address->city = $data['address']['city'];
            $address->city_area = $data['address']['city_area'];
            $address->city_district_fias_id = $data['address']['city_district_fias_id'];
            $address->city_district_kladr_id = $data['address']['city_district_kladr_id'];
            $address->city_district_with_type = $data['address']['city_district_with_type'];
            $address->city_district_type = $data['address']['city_district_type'];
            $address->city_district_type_full = $data['address']['city_district_type_full'];
            $address->city_district = $data['address']['city_district'];
            $address->settlement_fias_id = $data['address']['settlement_fias_id'];
            $address->settlement_kladr_id = $data['address']['settlement_kladr_id'];
            $address->settlement_with_type = $data['address']['settlement_with_type'];
            $address->settlement_type = $data['address']['settlement_type'];
            $address->settlement_type_full = $data['address']['settlement_type_full'];
            $address->settlement = $data['address']['settlement'];
            $address->street_fias_id = $data['address']['street_fias_id'];
            $address->street_kladr_id = $data['address']['street_kladr_id'];
            $address->street_with_type = $data['address']['street_with_type'];
            $address->street_type = $data['address']['street_type'];
            $address->street_type_full = $data['address']['street_type_full'];
            $address->street = $data['address']['street'];
            $address->house_fias_id = $data['address']['house_fias_id'];
            $address->house_kladr_id = $data['address']['house_kladr_id'];
            $address->house_type = $data['address']['house_type'];
            $address->house_type_full = $data['address']['house_type_full'];
            $address->house = $data['address']['house'];
            $address->block_type = $data['address']['block_type'];
            $address->block_type_full = $data['address']['block_type_full'];
            $address->block = $data['address']['block'];
            $address->flat_fias_id = $data['address']['flat_fias_id'];
            $address->flat_type = $data['address']['flat_type'];
            $address->flat_type_full = $data['address']['flat_type_full'];
            $address->flat = $data['address']['flat'];
            $address->flat_area = $data['address']['flat_area'];
            $address->square_meter_price = $data['address']['square_meter_price'];
            $address->flat_price = $data['address']['flat_price'];
            $address->postal_box = $data['address']['postal_box'];
            $address->fias_id = $data['address']['fias_id'];
            $address->fias_code = $data['address']['fias_code'];
            $address->fias_level = $data['address']['fias_level'];
            $address->kladr_id = $data['address']['kladr_id'];
            $address->capital_marker = $data['address']['capital_marker'];
            $address->okato = $data['address']['okato'];
            $address->oktmo = $data['address']['oktmo'];
            $address->tax_office = $data['address']['tax_office'];
            $address->tax_office_legal = $data['address']['tax_office_legal'];
            $address->timezone = $data['address']['timezone'];
            $address->geo_lat = $data['address']['geo_lat'];
            $address->geo_lon = $data['address']['geo_lon'];
            $address->beltway_hit = $data['address']['beltway_hit'];
            $address->beltway_distance = $data['address']['beltway_distance'];
            $address->qc_geo = $data['address']['qc_geo'];
            $address->qc_complete = $data['address']['qc_complete'];
            $address->qc_house = $data['address']['qc_house'];
            $address->qc = $data['address']['qc'];
            $address->unparsed_parts = $data['address']['unparsed_parts'];

            foreach ($data['address']['metro'] as $item) {
                $metro = new MetroDto();
                $metro->distance = $item['distance'];
                $metro->line = $item['line'];
                $metro->name = $item['name'];
                $address->metro [] = $metro;
            }

            $purchase->address = $address;

            $purchase->createdAt = new \DateTime($data['createdAt']);

            foreach ($data['products'] as $product) {
                $newProduct = new ProductDto();
                $newProduct->id = $product['id'];
                $newProduct->name = $product['name'];
                $newProduct->price = $product['price'];
                $newProduct->count = $product['count'];
                $purchase->products [] = $newProduct;
            }

            $this->assertEquals($data['user']['id'], $purchase->user->id);
        }
    }

    /**
     * @Revs(10000)
     */
    public function benchTransformCacheReflection(): void
    {
        $data = $this->getPurcheseObject();

        for ($i = 0; $i < 10; ++$i) {
            $purchase = (new Hydrator(new HydratorConfig(true)))
                ->create(PurchaseDto::class, $data);

            $this->assertEquals($data['user']['id'], $purchase->user->id);
        }
    }

    /**
     * @Revs(10000)
     */
    public function benchTransformReflection(): void
    {
        $data = $this->getPurcheseObject();

        for ($i = 0; $i < 10; ++$i) {
            $purchase = (new Hydrator())
                ->create(PurchaseDto::class, $data);

            $this->assertEquals($data['user']['id'], $purchase->user->id);
        }
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
                'createdAt' => '2023-04-10 12:30:23',
            ],
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
            "entrance" => null,
            "floor" => null,
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
            "fias_actuality_state" => "0",
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
