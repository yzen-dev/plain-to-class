<?php

namespace ClassTransformer\Reflection\Types;

use ClassTransformer\TransformUtils;
use ClassTransformer\Enums\TypeEnums;
use ClassTransformer\Attributes\ConvertArray;
use ClassTransformer\Reflection\RuntimeReflectionProperty;
use ReflectionNamedType;

/**
 * Class PropertyTypeFactory
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
class PropertyTypeFactory
{
    public static function create(RuntimeReflectionProperty $property)
    {
        $reflectionType = $property->property->getType();
        if ($reflectionType === null) {
            $type = TypeEnums::TYPE_MIXED;
            $isNullable = true;
            $isScalar = true;
        } elseif ($reflectionType instanceof ReflectionNamedType) {
            $type = $reflectionType->getName();
            $isNullable = $reflectionType->allowsNull();
            $isScalar = $reflectionType->isBuiltin();
        } else {
            $type = (string)$reflectionType;
            $isScalar = $reflectionType->isBuiltin();
            $isNullable = $reflectionType->allowsNull();
        }

        if (($isScalar && $type !== TypeEnums::TYPE_ARRAY) || $property->notTransform()) {
            return new ScalarType(
                $isNullable,
                $type,
                $isScalar
            );
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
                $isNullable,
                $type,
                $isScalar
            );
            $type->itemsType = $arrayType ?? TypeEnums::TYPE_MIXED;
            $type->isScalarItems = in_array($arrayType, [TypeEnums::TYPE_INTEGER, TypeEnums::TYPE_FLOAT, TypeEnums::TYPE_STRING, TypeEnums::TYPE_BOOLEAN, TypeEnums::TYPE_MIXED]);

            return $type;
        }
        
        if (function_exists('enum_exists') && !$isScalar && enum_exists($type)) {
            return new EnumType(
                $isNullable,
                $type,
                $isScalar
            );
        }

        if (!$isScalar) {
            return new TransformableType(
                $isNullable,
                $type,
                $isScalar
            );
        }

        return new PropertyType(
            $isNullable,
            $type,
            $isScalar
        );
    }
}
