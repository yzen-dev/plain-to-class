<?php

declare(strict_types=1);

namespace ClassTransformer\Reflection\Types;

use ReflectionNamedType;
use ClassTransformer\TransformUtils;
use ClassTransformer\Enums\TypeEnums;
use ClassTransformer\Attributes\ConvertArray;
use ClassTransformer\Reflection\RuntimeReflectionProperty;
use ReflectionType;

/**
 * Class PropertyTypeFactory
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
class PropertyTypeFactory
{
    /**
     * @param RuntimeReflectionProperty $property
     *
     * @return ArrayType|EnumType|PropertyType|ScalarType|TransformableType
     */
    public static function create(RuntimeReflectionProperty $property)
    {
        $reflectionType = $property->reflectionProperty->getType();

        $type = TypeEnums::TYPE_MIXED;
        $isScalar = true;
        $isNullable = true;

        if ($reflectionType instanceof ReflectionType) {
            $type = $reflectionType;
            $isNullable = $reflectionType->allowsNull();
        }
        if ($reflectionType instanceof ReflectionNamedType) {
            $type = $reflectionType->getName();
            $isScalar = $reflectionType->isBuiltin();
            $isNullable = $reflectionType->allowsNull();
        }

        if ($type === TypeEnums::TYPE_ARRAY) {
            $arrayTypeAttr = $property->getAttributeArguments(ConvertArray::class);

            if ($arrayTypeAttr !== null && isset($arrayTypeAttr[0])) {
                $arrayType = $arrayTypeAttr[0];
            } else {
                $arrayType = TransformUtils::getClassFromPhpDoc($property->getDocComment());
            }
            $arrayType ??= TypeEnums::TYPE_MIXED;
            $type = new ArrayType(
                $type,
                $isScalar,
                $isNullable
            );
            $type->itemsType = $arrayType ?? TypeEnums::TYPE_MIXED;
            $type->isScalarItems = in_array($arrayType, [TypeEnums::TYPE_INTEGER, TypeEnums::TYPE_FLOAT, TypeEnums::TYPE_STRING, TypeEnums::TYPE_BOOLEAN, TypeEnums::TYPE_MIXED]);

            return $type;
        }

        if ($isScalar || $property->notTransform()) {
            return new ScalarType(
                $type,
                $isScalar,
                $isNullable
            );
        }

        if (function_exists('enum_exists') && enum_exists($type)) {
            return new EnumType(
                $type,
                $isScalar,
                $isNullable
            );
        }

        return new TransformableType(
            $type,
            $isScalar,
            $isNullable
        );
    }
}
