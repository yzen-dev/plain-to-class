<?php
declare(strict_types=1);

namespace Tests\Integration\DTO;

use ClassTransformer\Attributes\ConvertArray;

class ArrayScalarDTO
{
    #[ConvertArray('string')]
    public ?array $stringList;
    public $id;
    
  
    #[ConvertArray('int')]
    public ?array $intList;
}
