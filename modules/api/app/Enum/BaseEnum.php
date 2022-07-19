<?php

namespace App\Enum;

use ReflectionClass;

class BaseEnum
{
    public static function listStr(): string
    {
        return join(',', static::list());
    }

    public static function list(): array
    {
        $oClass = new ReflectionClass(static::class);

        $enum_list = [];

        foreach ($oClass->getConstants() as $constantValue) {
            $enum_list[] = $constantValue;
        }

        return $enum_list;
    }
}