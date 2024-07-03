<?php

declare(strict_types=1);

namespace ClassTransformer;

use ClassTransformer\Reflection\ClassSchema;
use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\Exceptions\ValueNotFoundException;
use ClassTransformer\Exceptions\InvalidArgumentException;

/**
 * Class GenericInstance
 *
 * @psalm-api
 * @template T
 */
final class InstanceBuilder
{
    /** @var ClassSchema $classRepository */
    private ClassSchema $classSchema;

    /** @var ArgumentsRepository $argumentsRepository */
    private ArgumentsRepository $argumentsRepository;

    /**
     * @param ClassSchema $class
     * @param ArgumentsRepository $argumentsRepository
     */
    public function __construct(
        ClassSchema $class,
        ArgumentsRepository $argumentsRepository,
    ) {
        $this->classSchema = $class;

        $this->argumentsRepository = $argumentsRepository;
    }

    /**
     * @return T
     * @throws ClassNotFoundException|InvalidArgumentException|\ReflectionException
     */
    public function build(): mixed
    {
        $properties = $this->classSchema->getProperties();

        /** @var T $genericInstance */
        $genericInstance = $this->classSchema->makeInstance();

        foreach ($properties as $property) {
            try {
                $value = $this->argumentsRepository->getValue($property);
            } catch (ValueNotFoundException) {
                continue;
            }

            if ($property->hasSetMutator()) {
                $genericInstance->{TransformUtils::mutationSetterToCamelCase($property->name)}($value);
                continue;
            }

            $caster = new ValueCasting($property);
            $genericInstance->{$property->name} = $caster->castAttribute($value);
        }
        return $genericInstance;
    }
}
