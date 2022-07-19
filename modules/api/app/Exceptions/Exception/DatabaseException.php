<?php

namespace App\Exceptions\Exception;

use App\Exceptions\AppException;

final class DatabaseException extends AppException
{
    public static function UserCreationFailed()
    {
        return self::error('User Creation Failed', 3000);
    }

    public static function UserNotFound()
    {
        return self::error('User not found', 3001);
    }

}