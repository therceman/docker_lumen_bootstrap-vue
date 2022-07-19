<?php

namespace App\Validators;

use App\DTO\BaseDTO;
use App\DTO\ErrorDTO;

abstract class AbstractValidator
{
    protected ?BaseDTO $error;

    abstract public function validate();

    public function __construct()
    {
        $this->error = null;
        $this->validate();
    }

    protected function error(string $msg, int $code = 1): ErrorDTO
    {
        return $this->error = new ErrorDTO($msg, $code);
    }

    public function hasError(): bool
    {
        return $this->error !== null;
    }

    public function getErrorMsg(): string
    {
        if ($this->error === null)
            return '';

        return $this->error->getErrorMsg();
    }

    public function getErrorCode(): bool
    {
        if ($this->error === null)
            return -1;

        return $this->error->getErrorCode();
    }
}