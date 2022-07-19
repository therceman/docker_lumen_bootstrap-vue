<?php

namespace App\Exceptions;

use Dotenv\Exception\ExceptionInterface;
use RuntimeException;

class AppException extends RuntimeException implements ExceptionInterface
{
    const LOG_DATA_SEP = '|LOG_DATA|';

    /**
     * @param string $msg
     * @param int $code
     * @param mixed $logData
     *
     * @return static|AppException|RuntimeException
     */
    public static function error(string $msg, int $code, $logData = null)
    {
        $error = new static($msg, $code);

        if ($logData !== null)
            $error->setLogData($logData);

        return $error;
    }

    /**
     * @param $logData
     * @return static|AppException|RuntimeException
     */
    public function setLogData($logData)
    {
        $this->message = explode(self::LOG_DATA_SEP, $this->message)[0];
        $this->message .= self::LOG_DATA_SEP . (json_encode($logData));

        return $this;
    }
}