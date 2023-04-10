<?php

namespace Tests\Benchmark;

use PHPUnit\Framework\TestCase;
use Tests\Benchmark\DTO\AddressClean;
use ClassTransformer\ClassTransformer;
use ClassTransformer\ClassTransformerConfig;

/**
 * Class AddressBench
 *
 * @package Tests\Benchmark
 *
 * ./vendor/bin/phpbench run tests/Benchmark/AddressBench.php --report=default
 */
class AddressBench extends TestCase
{

    /**
     * @Revs(10000)
     */
    public function benchBase(): void
    {
        ClassTransformer::transform(AddressClean::class, $this->getData());
    }

    /**
     * @Revs(10000)
     */
    public function benchCache(): void
    {
        ClassTransformerConfig::$cache = true;
        ClassTransformer::transform(AddressClean::class, $this->getData());
    }


    private function getData()
    {
        return json_decode('{
    "source": "мск сухонска 11/-89",
    "result": "г Москва, ул Сухонская, д 11, кв 89",
    "postal_code": "127642",
    "country": "Россия",
    "country_iso_code": "RU",
    "federal_district": "Центральный",
    "region_fias_id": "0c5b2444-70a0-4932-980c-b4dc0d3f02b5",
    "region_kladr_id": "7700000000000",
    "region_iso_code": "RU-MOW",
    "region_with_type": "г Москва",
    "region_type": "г",
    "region_type_full": "город",
    "region": "Москва",
    "area_fias_id": null,
    "area_kladr_id": null,
    "area_with_type": null,
    "area_type": null,
    "area_type_full": null,
    "area": null,
    "city_fias_id": null,
    "city_kladr_id": null,
    "city_with_type": null,
    "city_type": null,
    "city_type_full": null,
    "city": null,
    "city_area": "Северо-восточный",
    "city_district_fias_id": null,
    "city_district_kladr_id": null,
    "city_district_with_type": "р-н Северное Медведково",
    "city_district_type": "р-н",
    "city_district_type_full": "район",
    "city_district": "Северное Медведково",
    "settlement_fias_id": null,
    "settlement_kladr_id": null,
    "settlement_with_type": null,
    "settlement_type": null,
    "settlement_type_full": null,
    "settlement": null,
    "street_fias_id": "95dbf7fb-0dd4-4a04-8100-4f6c847564b5",
    "street_kladr_id": "77000000000283600",
    "street_with_type": "ул Сухонская",
    "street_type": "ул",
    "street_type_full": "улица",
    "street": "Сухонская",
    "house_fias_id": "5ee84ac0-eb9a-4b42-b814-2f5f7c27c255",
    "house_kladr_id": "7700000000028360004",
    "house_cadnum": "77:02:0004008:1017",
    "house_type": "д",
    "house_type_full": "дом",
    "house": "11",
    "block_type": null,
    "block_type_full": null,
    "block": null,
    "entrance": null,
    "floor": null,
    "flat_fias_id": "f26b876b-6857-4951-b060-ec6559f04a9a",
    "flat_cadnum": "77:02:0004008:4143",
    "flat_type": "кв",
    "flat_type_full": "квартира",
    "flat": "89",
    "flat_area": "34.6",
    "square_meter_price": "239953",
    "flat_price": "8302374",
    "postal_box": null,
    "fias_id": "f26b876b-6857-4951-b060-ec6559f04a9a",
    "fias_code": "77000000000000028360004",
    "fias_level": "9",
    "fias_actuality_state": "0",
    "kladr_id": "7700000000028360004",
    "capital_marker": "0",
    "okato": "45280583000",
    "oktmo": "45362000",
    "tax_office": "7715",
    "tax_office_legal": "7715",
    "timezone": "UTC+3",
    "geo_lat": "55.8782557",
    "geo_lon": "37.65372",
    "beltway_hit": "IN_MKAD",
    "beltway_distance": null,
    "qc_geo": 0,
    "qc_complete": 0,
    "qc_house": 2,
    "qc": 0,
    "unparsed_parts": null,
    "metro": [
      {
        "distance": 1.1,
        "line": "Калужско-Рижская",
        "name": "Бабушкинская"
      },
      {
        "distance": 1.2,
        "line": "Калужско-Рижская",
        "name": "Медведково"
      },
      {
        "distance": 2.5,
        "line": "Калужско-Рижская",
        "name": "Свиблово"
      }
    ]
  }');
    }
}
