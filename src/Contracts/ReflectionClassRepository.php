<?php

namespace ClassTransformer\Contracts;

/**
 * @psalm-api
 */
interface ReflectionClassRepository
{
    /**
     * @return ReflectionProperty[]
     */
    public function getProperties(): array;

    /**
     * @return string
     */
    public function getClass(): string;
}
