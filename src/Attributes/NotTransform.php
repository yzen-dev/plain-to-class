<?php

declare(strict_types=1);

namespace ClassTransformer\Attributes;

use Attribute;

/**
 * Attribute for properties that don't need to be converted
 *
 * @psalm-api
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
final class NotTransform
{
}
