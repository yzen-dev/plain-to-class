<?php

declare(strict_types=1);

namespace ClassTransformer;

use ClassTransformer\Attributes\WritingStyle;
use ClassTransformer\Reflection\ClassProperty;
use ClassTransformer\Exceptions\ValueNotFoundException;
use function sizeof;
use function is_array;
use function func_get_args;
use function array_intersect;
use function array_key_exists;

/**
 * Class GenericProperty
 *
 * @psalm-api
 */
final class ArgumentsRepository
{
    /** @var array<mixed> $args */
    private array $args;

    /**
     *
     * @param iterable<mixed>|object|string ...$args
     */
    public function __construct(...$args)
    {
        // Unpacking named arguments
        $input = sizeof(func_get_args()) === 1 ? $args[0] : $args;

        if (!is_array($input)) {
            if (is_string($input)) {
                $input = json_decode($input, true);
            } else {
                $input = (array)$input;
            }
        }

        $this->args = $input;
    }

    /**
     * @param ClassProperty $genericProperty
     *
     * @return mixed
     * @throws ValueNotFoundException
     */
    public function getValue(ClassProperty $genericProperty): mixed
    {
        if (array_key_exists($genericProperty->name, $this->args)) {
            return $this->args[$genericProperty->name];
        }

        $aliases = $genericProperty->getAliases();

        if (!empty($aliases)) {
            foreach ($aliases as $alias) {
                if (array_key_exists($alias, $this->args)) {
                    return $this->args[$alias];
                }
            }
        }

        $styles = $genericProperty->getAttributeArguments(WritingStyle::class);

        if ($styles === null) {
            throw new ValueNotFoundException();
        }

        $snakeCase = TransformUtils::attributeToSnakeCase($genericProperty->name);
        if (array_key_exists($snakeCase, $this->args) && sizeof(array_intersect([WritingStyle::STYLE_SNAKE_CASE, WritingStyle::STYLE_ALL], $styles)) > 0) {
            return $this->args[$snakeCase];
        }

        $camelCase = TransformUtils::attributeToCamelCase($genericProperty->name);
        if (array_key_exists($camelCase, $this->args) && sizeof(array_intersect([WritingStyle::STYLE_CAMEL_CASE, WritingStyle::STYLE_ALL], $styles)) > 0) {
            return $this->args[$camelCase];
        }

        throw new ValueNotFoundException();
    }
}
