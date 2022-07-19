<?php

namespace App\Model;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use ReflectionClass;

abstract class AbstractModel
{
    abstract public static function getTable(): string;
    abstract public static function create(Blueprint $table);

    public static function createMigrationSchema()
    {
        Schema::create(static::getTable(), function (Blueprint $table) {
            static::create($table);
        });
    }

    public static function dropMigrationSchema()
    {
        Schema::dropIfExists(static::getTable());
    }

    public static function getQueryBuilder(): Builder
    {
        return DB::table(static::getTable());
    }
}