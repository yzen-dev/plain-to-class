<?php
declare(strict_types=1);

namespace Tests\DTO;

class ArrayScalarDTO
{
    public $id;
    
    /** @var array<string>  */
    public array $history;
}
