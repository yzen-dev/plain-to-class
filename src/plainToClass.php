<?php

if (!function_exists('plainToClass')) {
    /**
     * Class-transformer function to transform our object into a typed object
     * @template T
     *
     * @param T $class
     * @param   $args
     *
     * @return T
     * @throws ReflectionException
     */
    function plainToClass($class, $args)
    {
        $instance = new $class;
        if ($args !== null) {
            if (method_exists($class, 'plainToClass')) {
                return $class::plainToClass($args);
            }
            $refInstance = new ReflectionClass($class);
            if (is_object($args)) {
                $refArgsObject = new ReflectionObject($args);
                foreach ($refInstance->getProperties() as $item) {
                    if ($refArgsObject->hasProperty($item->name)) {
                        $scalarTypes = ['int', 'float', 'string', 'bool'];

                        $propertyClass = $refInstance->getProperty($item->name);
                        $propertyClassType = $refInstance->getProperty($item->name)->getType()->getName();

                        $propertyArgs = $refArgsObject->getProperty($item->name);
                        $propertyArgsType = getType($args->{$item->name});

                        ## if scalar type
                        if (in_array($propertyClassType, $scalarTypes) && in_array($propertyArgsType, $scalarTypes)) {
                            $instance->{$item->name} = $args->{$item->name};
                            continue;
                        }

                        if ($propertyClassType === 'array' && $propertyArgsType === 'array') {
                            if ($propertyClass->getDocComment()) {
                                preg_match('/array<([a-zA-Z\d\\\]+)>/m', $propertyClass->getDocComment(), $docType);
                                $docType = $docType[1] ?? null;

                                if ($docType && class_exists($docType)) {
                                    foreach ($args->{$item->name} as $el) {
                                        $instance->{$item->name}[] = plainToClass($docType, $el);
                                    }
                                }
                            }
                            continue;
                        }

                        if ($propertyClassType && class_exists($propertyClassType)) {
                            $instance->{$item->name} = plainToClass($propertyClassType, $args->{$item->name});
                            continue;
                        }
                        $instance->{$item->name} = $args->{$item->name};
                    }
                }
            } else {
                foreach ($refInstance->getProperties() as $item) {
                    if (array_key_exists($item->name, $args)) {
                        $scalarTypes = ['int', 'float', 'string', 'bool'];

                        $propertyClass = $refInstance->getProperty($item->name);
                        $propertyClassType = $refInstance->getProperty($item->name)->getType()->getName();
                        ## if scalar type
                        if (in_array($propertyClassType, $scalarTypes)) {
                            $instance->{$item->name} = $args[$item->name];
                            continue;
                        }

                        if ($propertyClassType === 'array') {
                            if ($propertyClass->getDocComment()) {
                                preg_match('/array<([a-zA-Z\d\\\]+)>/m', $propertyClass->getDocComment(), $docType);
                                $docType = $docType[1] ?? null;

                                if ($docType && class_exists($docType)) {
                                    foreach ($args[$item->name] as $el) {
                                        $instance->{$item->name}[] = plainToClass($docType, $el);
                                    }
                                }
                            }
                            continue;
                        }

                        if ($propertyClassType && class_exists($propertyClassType)) {
                            $instance->{$item->name} = plainToClass($propertyClassType, $args[$item->name]);
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
