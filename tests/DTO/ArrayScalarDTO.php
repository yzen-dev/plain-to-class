<?php
declare(strict_types=1);

namespace Tests\DTO;

class ArrayScalarDTO
{
    public $id;
    
    /** @var null|array<string>  */
    public ?array $products;
}
