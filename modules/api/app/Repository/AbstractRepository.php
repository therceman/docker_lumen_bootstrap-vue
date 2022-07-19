<?php

namespace App\Repository;

use App\Entity\AbstractEntity;
use Illuminate\Database\Query\Builder;
use Ramsey\Uuid\Uuid;

abstract class AbstractRepository
{
    const ALL_COLUMNS = ['*'];

    abstract static function getEntityClass(): string;
    abstract static function getQueryBuilder(): Builder;

    public static function find($id, array $columns = self::ALL_COLUMNS): ?AbstractEntity
    {
        $dbRes = static::getQueryBuilder()->find($id, $columns);

        /** @var AbstractEntity $entityClass */
        $entityClass = static::getEntityClass();

        return $entityClass::fromDBRes($dbRes);
    }
    
    protected static function countByUUID($uuid, $uuidField = 'uuid'): int
    {
        return static::getQueryBuilder()
            ->where($uuidField, $uuid)
            ->count($uuidField);
    }

    protected static function createUUID(): string
    {
        $uuid = Uuid::uuid4()->toString();

        if (static::countByUUID($uuid) > 0)
            $uuid = static::createUUID();

        return $uuid;
    }
}