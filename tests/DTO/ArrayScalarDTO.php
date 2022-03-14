<?php
declare(strict_types=1);

namespace Tests\DTO;

use ClassTransformer\Attributes\ConvertArray;

class ArrayScalarDTO
{
    public $id;
    
    /** @var null|array<string>  */
    #[ConvertArray('string')]
    public ?array $products;
}
