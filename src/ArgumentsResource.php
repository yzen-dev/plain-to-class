<?php

declare(strict_types=1);

namespace ClassTransformer;

use ClassTransformer\Attributes\FieldAlias;
use ClassTransformer\Attributes\WritingStyle;
use ClassTransformer\Contracts\ReflectionProperty;
use ClassTransformer\Exceptions\ValueNotFoundException;

use function is_string;
use function array_intersect;
use function array_key_exists;
use function func_get_args;
use function sizeof;

/**
 * Class GenericProperty
 *
 * @psalm-api
 */
final class ArgumentsResource
{
    /** @var array<mixed> $args */
    private array $args;

    /**
     *
     * @param iterable<mixed>|array ...$args
     */
    public function __construct(...$args)
    {
        // Unpacking named arguments
        $inArgs = sizeof(func_get_args()) === 1 ? $args[0] : $args;

        if (!is_array($inArgs)) {
            $inArgs = (array)$inArgs;
        }

        $this->args = $inArgs;
    }

    /**
     * @param ReflectionProperty $genericProperty
     *
     * @return mixed|object|array<mixed>|null
     * @throws ValueNotFoundException
     */
    public function getValue(ReflectionProperty $genericProperty): mixed
    {
        if (array_key_exists($genericProperty->getName(), $this->args)) {
            return $this->args[$genericProperty->getName()];
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

        $snakeCase = TransformUtils::attributeToSnakeCase($genericProperty->getName());
        if (array_key_exists($snakeCase, $this->args) && sizeof(array_intersect([WritingStyle::STYLE_SNAKE_CASE, WritingStyle::STYLE_ALL], $styles)) > 0) {
            return $this->args[$snakeCase];
        }

        $camelCase = TransformUtils::attributeToCamelCase($genericProperty->getName());
        if (array_key_exists($camelCase, $this->args) && sizeof(array_intersect([WritingStyle::STYLE_CAMEL_CASE, WritingStyle::STYLE_ALL], $styles)) > 0) {
            return $this->args[$camelCase];
        }

        throw new ValueNotFoundException();
    }
}
