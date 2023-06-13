<?php

namespace ClassTransformer\Contracts;

/**
 * @psalm-api
 */
interface ReflectionClassRepository
{
    /**
     * @return array<ReflectionProperty>
     */
    public function getProperties(): array;

    /**
     * @return string
     */
    public function getClass(): string;
}
