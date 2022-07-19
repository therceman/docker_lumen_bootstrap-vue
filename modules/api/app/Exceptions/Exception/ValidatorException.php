<?php

namespace App\Exceptions\Exception;


use App\Exceptions\AppException;

final class ValidatorException extends AppException
{
    public static function AuthorizationHeaderIsMissing()
    {
        return self::error('Authorization header is missing', 1000);
    }
}