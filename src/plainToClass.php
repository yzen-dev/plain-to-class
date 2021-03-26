<?php

if (!function_exists('plainToClass')) {
    /**
     * Class-transformer function to transform our object into a typed object
     * @template T
     *
     * @param class-string<T> $className
     * @param mixed $args
     *
     * @return T
     * @throws ReflectionException
     */
    function plainToClass(string $className, $args)
    {
        $instance = new $className();
        if ($args !== null) {
            if (method_exists($className, 'plainToClass')) {
                return $className::plainToClass($args);
            }
            $refInstance = new ReflectionClass($className);
            if (is_object($args)) {
                $refArgsObject = new ReflectionObject($args);
                foreach ($refInstance->getProperties() as $item) {
                    if ($refArgsObject->hasProperty($item->name)) {
                        $propertyClass = $refInstance->getProperty($item->name);
                        $propertyClassType = $propertyClass->getType();
                        $propertyClassTypeName = $propertyClassType !== null ? $propertyClassType->getName() : false;
                        $propertyArgsType = getType($args->{$item->name});

                        if ($propertyClassTypeName === 'array' && $propertyArgsType === 'array') {
                            $doc = $propertyClass->getDocComment();
                            if ($doc) {
                                preg_match('/array<([a-zA-Z\d\\\]+)>/m', $doc, $docType);
                                $docType = $docType[1] ?? null;

                                if ($docType && class_exists($docType)) {
                                    foreach ($args->{$item->name} as $el) {
                                        $instance->{$item->name}[] = plainToClass($docType, $el);
                                    }
                                }
                            }
                            continue;
                        }

                        if ($propertyClassTypeName && class_exists($propertyClassTypeName)) {
                            $instance->{$item->name} = plainToClass($propertyClassTypeName, $args->{$item->name});
                            continue;
                        }
                        $instance->{$item->name} = $args->{$item->name};
                    }
                }
            } else {
                foreach ($refInstance->getProperties() as $item) {
                    if (array_key_exists($item->name, $args)) {
                        $propertyClass = $refInstance->getProperty($item->name);
                        $propertyClassType = $propertyClass->getType();
                        $propertyClassTypeName = $propertyClassType !== null ? $propertyClassType->getName() : false;

                        if ($propertyClassTypeName === 'array') {
                            $doc = $propertyClass->getDocComment();
                            if ($doc) {
                                preg_match('/array<([a-zA-Z\d\\\]+)>/m', $doc, $docType);
                                $docType = $docType[1] ?? null;

                                if ($docType && class_exists($docType)) {
                                    foreach ($args[$item->name] as $el) {
                                        $instance->{$item->name}[] = plainToClass($docType, $el);
                                    }
                                }
                            }
                            continue;
                        }

                        if ($propertyClassTypeName && class_exists($propertyClassTypeName)) {
                            $instance->{$item->name} = plainToClass($propertyClassTypeName, $args[$item->name]);
                            continue;
                        }
                        $instance->{$item->name} = $args[$item->name];
                    }
                }
            }
        }
        return $instance;
    }
}
