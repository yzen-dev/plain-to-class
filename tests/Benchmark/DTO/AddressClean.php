<?php

namespace Tests\Benchmark\DTO;

namespace Tests\Benchmark\DTO;

/**
 * DTO для работы с поиском адресов по методам https://dadata.ru/api/clean/address/
 *
 * @package AlexBklnv\Dadata
 * @author AlexBklnv <alexbklnv@yandex.ru>
 */
class AddressClean
{
    /**
     * @var null|string Исходный адрес одной строкой
     */
    public ?string $source;
    
    /**
     * @var null|string Стандартизованный адрес одной строкой
     */
    public ?string $result;
    
    /**
     * @var null|string Индекс
     */
    public ?string $postal_code;
    
    /**
     * @var null|string Страна
     */
    public ?string $country;
    
    /**
     * @var null|string Cтрана iso alfa2
     */
    public ?string $country_iso_code;
    
    /**
     * @var null|string Федеральный округ
     */
    public ?string $federal_district;
    
    /**
     * @var null|string Код ФИАС региона
     */
    public ?string $region_fias_id;
    
    /**
     * @var null|string Код КЛАДР региона
     */
    public ?string $region_kladr_id;
    
    /**
     * @var null|string ISO-код региона
     */
    public ?string $region_iso_code;
    
    /**
     * @var null|string Регион с типом
     */
    public ?string $region_with_type;
    
    /**
     * @var null|string Тип региона (сокращенный)
     */
    public ?string $region_type;
    
    /**
     * @var null|string Тип региона
     */
    public ?string $region_type_full;
    
    /**
     * @var null|string Регион
     */
    public ?string $region;
    
    /**
     * @var null|string Код ФИАС района в регионе
     */
    public ?string $area_fias_id;
    
    /**
     * @var null|string Код КЛАДР района в регионе
     */
    public ?string $area_kladr_id;
    
    /**
     * @var null|string Район в регионе с типом
     */
    public ?string $area_with_type;
    
    /**
     * @var null|string Тип района в регионе (сокращенный)
     */
    public ?string $area_type;
    
    /**
     * @var null|string Тип района в регионе
     */
    public ?string $area_type_full;
    
    /**
     * @var null|string Район в регионе
     */
    public ?string $area;
    
    /**
     * @var null|string Код ФИАС города
     */
    public ?string $city_fias_id;
    
    /**
     * @var null|string Код КЛАДР города
     */
    public ?string $city_kladr_id;
    
    /**
     * @var null|string Город с типом
     */
    public ?string $city_with_type;
    
    /**
     * @var null|string Тип города (сокращенный)
     */
    public ?string $city_type;
    
    /**
     * @var null|string Тип города
     */
    public ?string $city_type_full;
    
    /**
     * @var null|string Город
     */
    public ?string $city;
    
    /**
     * @var null|string Административный округ (только для Москвы)
     */
    public ?string $city_area;
    
    /**
     * @var null|string Код ФИАС района города (не заполняется)
     */
    public ?string $city_district_fias_id;
    
    /**
     * @var null|string Код КЛАДР района города (не заполняется)
     */
    public ?string $city_district_kladr_id;
    
    /**
     * @var null|string Район города с типом
     */
    public ?string $city_district_with_type;
    
    /**
     * @var null|string Тип района города (сокращенный)
     */
    public ?string $city_district_type;
    
    /**
     * @var null|string Тип района города
     */
    public ?string $city_district_type_full;
    
    /**
     * @var null|string Район города
     */
    public ?string $city_district;
    
    /**
     * @var null|string Код ФИАС нас. пункта
     */
    public ?string $settlement_fias_id;
    
    /**
     * @var null|string Код КЛАДР нас. пункта
     */
    public ?string $settlement_kladr_id;
    
    /**
     * @var null|string Населенный пункт с типом
     */
    public ?string $settlement_with_type;
    
    /**
     * @var null|string Тип населенного пункта (сокращенный)
     */
    public ?string $settlement_type;
    
    /**
     * @var null|string Тип населенного пункта
     */
    public ?string $settlement_type_full;
    
    /**
     * @var null|string Населенный пункт
     */
    public ?string $settlement;
    
    /**
     * @var null|string Код ФИАС улицы
     */
    public ?string $street_fias_id;
    
    /**
     * @var null|string Код КЛАДР улицы
     */
    public ?string $street_kladr_id;
    
    /**
     * @var null|string Улица с типом
     */
    public ?string $street_with_type;
    
    /**
     * @var null|string Тип улицы (сокращенный)
     */
    public ?string $street_type;
    
    /**
     * @var null|string Тип улицы
     */
    public ?string $street_type_full;
    
    /**
     * @var null|string Улица
     */
    public ?string $street;
    
    /**
     * @var null|string Код ФИАС дома
     */
    public ?string $house_fias_id;
    
    /**
     * @var null|string Код КЛАДР дома
     */
    public ?string $house_kladr_id;
    
    /**
     * @var null|string Тип дома (сокращенный)
     */
    public ?string $house_type;
    
    /**
     * @var null|string Тип дома
     */
    public ?string $house_type_full;
    
    /**
     * @var null|string Дом
     */
    public ?string $house;
    
    /**
     * @var null|string Тип корпуса/строения (сокращенный)
     */
    public ?string $block_type;
    
    /**
     * @var null|string Тип корпуса/строения
     */
    public ?string $block_type_full;
    
    /**
     * @var null|string Корпус/строение
     */
    public ?string $block;
    
    /**
     * @var null|string Тип квартиры (сокращенный)
     */
    public ?string $flat_type;
    
    /**
     * @var null|string Тип квартиры
     */
    public ?string $flat_type_full;
    
    /**
     * @var null|string Квартира
     */
    public ?string $flat;
    
    /**
     * @var null|float Площадь квартиры
     */
    public ?float $flat_area;
    
    /**
     * @var null|string Рыночная стоимость м²
     */
    public ?string $square_meter_price;
    
    /**
     * @var null|string Рыночная стоимость квартиры
     */
    public ?string $flat_price;
    
    /**
     * @var null|string Абонентский ящик
     */
    public ?string $postal_box;
    
    /**
     * @var null|string Код ФИАС:
     * @see \AlexBklnv\DaData\Enums\FiasIdEnum
     */
    public ?string $fias_id;
    
    /**
     * @var null|int Уровень детализации, до которого адрес найден в ФИАС
     * @see \AlexBklnv\DaData\Enums\FiasLevelEnum
     */
    public ?int $fias_level;
    
    /**
     * @var null|string Код КЛАДР
     */
    public ?string $kladr_id;
    
    /**
     * @var null|int Является ли город центром
     * @see \AlexBklnv\DaData\Enums\CapitalMarkerEnum
     */
    public ?int $capital_marker;
    
    /**
     * @var null|string Код ОКАТО
     */
    public ?string $okato;
    
    /**
     * @var null|string Код ОКТМО
     */
    public ?string $oktmo;
    
    /**
     * @var null|string Код ИФНС для физических лиц
     */
    public ?string $tax_office;
    
    /**
     * @var null|string Код ИФНС для организаций (не заполняется)
     */
    public ?string $tax_office_legal;
    
    /**
     * @var null|string Часовой пояс
     */
    public ?string $timezone;
    
    /**
     * @var null|float Координаты: широта
     */
    public ?float $geo_lat;
    
    /**
     * @var null|float Координаты: долгота
     */
    public ?float $geo_lon;
    
    /**
     * @var null|string Внутри кольцевой?
     * @see \AlexBklnv\DaData\Enums\BeltwayHitEnum
     */
    public ?string $beltway_hit;
    
    /**
     * @var null|int Расстояние от кольцевой в км. Заполнено, только если beltway_hit = OUT_MKAD или OUT_KAD, иначе пустое.
     */
    public ?int $beltway_distance;
    
    /**
     * @var null|int Код точности координат
     * @see \AlexBklnv\DaData\Enums\QcEnum
     */
    public ?int $qc;
    
    /**
     * @var null|int Код точности координат
     * @see \AlexBklnv\DaData\Enums\QcGeoEnum
     */
    public ?int $qc_geo;
    
    /**
     * @var null|int Код полноты
     * @see \AlexBklnv\DaData\Enums\QcCompeteEnum
     */
    public ?int $qc_complete;
    
    /**
     * @var null|int Признак наличия дома в ФИАС - уточняет вероятность успешной доставки письма
     * @link https://dadata.ru/api/clean/address/#qc_house
     */
    public ?int $qc_house;
    
    /**
     * @var null|string Нераспознанная часть адреса. Для адреса
     */
    public ?string $unparsed_parts;
    
    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->result;
    }
}
