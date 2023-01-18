<?php

namespace ClassTransformer;

use ClassTransformer\Attributes\WritingStyle;
use ClassTransformer\Exceptions\ValueNotFoundException;

use function sizeof;
use function in_array;
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
     * @param array<mixed>|object|null $args
     */
    public function __construct(...$args)
    {
        // Unpacking named arguments
        $inArgs = sizeof(func_get_args()) === 1 ? $args[0] : $args;

        if (is_object($inArgs)) {
            $inArgs = (array)$inArgs;
        }

        $this->args = $inArgs ?? [];
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

        $writingStyle = $genericProperty->getAttribute(WritingStyle::class);

        if (empty($writingStyle)) {
            throw new ValueNotFoundException();
        }

        $styles = $writingStyle->getArguments();

        if (empty($styles)) {
            throw new ValueNotFoundException();
        }

        $snakeCase = TransformUtils::strToSnakeCase($genericProperty->name);
        $camelCase = TransformUtils::strToCamelCase($genericProperty->name);

        if (
            (in_array(WritingStyle::STYLE_SNAKE_CASE, $styles, true) || in_array(WritingStyle::STYLE_ALL, $styles, true)) &
            array_key_exists($snakeCase, $this->args)
        ) {
            return $this->args[$snakeCase];
        }
        if (
            (in_array(WritingStyle::STYLE_CAMEL_CASE, $styles, true) || in_array(WritingStyle::STYLE_ALL, $styles, true)) &
            array_key_exists($camelCase, $this->args)
        ) {
            return $this->args[$camelCase];
        }
        throw new ValueNotFoundException();
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }
}
