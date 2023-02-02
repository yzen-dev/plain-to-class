<?php

namespace ClassTransformer;

use ClassTransformer\Attributes\FieldAlias;
use ClassTransformer\Attributes\WritingStyle;
use ClassTransformer\Exceptions\ValueNotFoundException;

use function sizeof;
use function is_object;
use function func_get_args;
use function array_intersect;
use function array_key_exists;

/**
 * Class GenericProperty
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
final class ArgumentsResource
{
    /** @var array<mixed> $args */
    private array $args;

    /**
     * @param iterable<mixed>|mixed ...$args
     */
    public function __construct(...$args)
    {
        // Unpacking named arguments
        $inArgs = sizeof(func_get_args()) === 1 ? $args[0] : $args;

        $inArgs = (array)$inArgs;

        $this->args = $inArgs;
    }

    /**
     * @param GenericProperty $genericProperty
     *
     * @return mixed|object|array<mixed>|null
     * @throws ValueNotFoundException
     */
    public function getValue(GenericProperty $genericProperty): mixed
    {
        if (array_key_exists($genericProperty->name, $this->args)) {
            return $this->args[$genericProperty->name];
        }

        $aliasesAttr = $genericProperty->getAttribute(FieldAlias::class);

        if ($aliasesAttr !== null) {
            $aliases = $aliasesAttr->getArguments();
            if (!empty($aliases)) {
                $aliases = $aliases[0];
                if (is_string($aliases)) {
                    $aliases = [$aliases];
                }
                foreach ($aliases as $alias) {
                    if (array_key_exists($alias, $this->args)) {
                        return $this->args[$alias];
                    }
                }
            }
        }

        $writingStyle = $genericProperty->getAttribute(WritingStyle::class);

        if ($writingStyle === null) {
            throw new ValueNotFoundException();
        }

        $styles = $writingStyle->getArguments();

        if (empty($styles)) {
            throw new ValueNotFoundException();
        }

        $snakeCase = TransformUtils::attributeToSnakeCase($genericProperty->name);
        if (sizeof(array_intersect([WritingStyle::STYLE_SNAKE_CASE, WritingStyle::STYLE_ALL], $styles)) > 0 & array_key_exists($snakeCase, $this->args)) {
            return $this->args[$snakeCase];
        }

        $camelCase = TransformUtils::attributeToCamelCase($genericProperty->name);
        if (sizeof(array_intersect([WritingStyle::STYLE_CAMEL_CASE, WritingStyle::STYLE_ALL], $styles)) > 0 & array_key_exists($camelCase, $this->args)) {
            return $this->args[$camelCase];
        }

        throw new ValueNotFoundException();
    }
}