<?php

declare(strict_types=1);

namespace ClassTransformer;

use ClassTransformer\Exceptions\ClassNotFoundException;
use ClassTransformer\Exceptions\ValueNotFoundException;

/**
 * Class GenericInstance
 *
 * @psalm-api
 * @template T
 */
final class InstanceBuilder
{
    private HydratorConfig $config;

    /** @var ClassRepository $classRepository */
    private ClassRepository $class;

    /** @var ArgumentsRepository $argumentsRepository */
    private ArgumentsRepository $argumentsRepository;


    /**
     * @param ClassRepository $class
     * @param ArgumentsRepository $argumentsRepository
     */
    public function __construct(ClassRepository $class, ArgumentsRepository $argumentsRepository, HydratorConfig $config = null)
    {
        $this->class = $class;
        $this->config = $config ?? new HydratorConfig();

        $this->argumentsRepository = $argumentsRepository;
    }

    /**
     * @return T
     * @throws ClassNotFoundException
     */
    public function build(): mixed
    {
        $properties = $this->class->getProperties();
        /** @var T $genericInstance */
        $genericInstance = new ($this->class->getClass());

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

            $caster = new ValueCasting($property, $this->config);
            $genericInstance->{$property->name} = $caster->castAttribute($value);
        }
        return $genericInstance;
    }
}
