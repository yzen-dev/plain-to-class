<?php

declare(strict_types=1);

namespace Tests\Integration\DTO;

use ClassTransformer\Attributes\FieldAlias;

class WithAliasDTO
{
    #[FieldAlias('userFio')]
    public string $fio;

    #[FieldAlias(['email', 'phone'])]
    public string $contact;
}
