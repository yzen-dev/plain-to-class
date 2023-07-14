<?php

namespace ClassTransformer;

use ClassTransformer\Contracts\ReflectionProperty;
use ClassTransformer\Contracts\ReflectionClassRepository;

/**
 * Class ClassTemplate
 *
 * @author yzen.dev <yzen.dev@gmail.com>
 */
final class ClassRepository
{
    /** @var string $class */
    private string $class;

    /** @var ReflectionClassRepository $class */
    private ReflectionClassRepository $classRepository;

    /**
     * @var array<string,ReflectionProperty[]>
     */
    private static array $propertiesTypesCache = [];


    /**
     * @param string $class
     * @param ReflectionClassRepository $classRepository
     */
    public function __construct(
        string $class,
        ReflectionClassRepository $classRepository
    ) {
        $this->class = $class;
        $this->classRepository = $classRepository;
    }

    /**
     * @return ReflectionProperty[]
     */
    public function getProperties(): array
    {
        if (isset(self::$propertiesTypesCache[$this->class])) {
            return self::$propertiesTypesCache[$this->class];
        }

        return self::$propertiesTypesCache[$this->class] = $this->classRepository->getProperties();
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }
}
