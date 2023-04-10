<?php

namespace ClassTransformer\Reflection;

interface ReflectionClass
{
    public function getProperties(): array;

    public function getClass(): string;
}
