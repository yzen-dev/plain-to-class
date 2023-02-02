<?php
declare(strict_types=1);

namespace Tests\Integration\DTO;

use ClassTransformer\Attributes\ConvertArray;

class ArrayScalarDTO
{
    public $id;
    
    #[ConvertArray('string')]
    public ?array $stringList;
    #[ConvertArray('int')]
    public ?array $intList;
}
