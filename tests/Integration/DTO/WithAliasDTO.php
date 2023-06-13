<?php

declare(strict_types=1);

namespace Tests\Integration\DTO;

use ClassTransformer\Attributes\FieldAlias;

class WithAliasDTO
{
    #[FieldAlias(['email', 'phone'])]
    public string $contact;
    #[FieldAlias('userFio')]
    public string $fio;

    
}
