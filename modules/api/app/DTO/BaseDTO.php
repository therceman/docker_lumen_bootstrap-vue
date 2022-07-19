<?php

namespace App\DTO;


use Illuminate\Http\Client\Response;
use phpDocumentor\Reflection\Types\Static_;
use Symfony\Component\HttpFoundation\Request;

class BaseDTO
{
    /**
     * @return static|BaseDTO|null
     */
    public static function fromDTO(BaseDTO $dto): ?BaseDTO
    {
        return self::fromArray($dto->all());
    }

    public function hasError(): bool
    {
        return strpos(static::class, 'ErrorDTO') !== false;
    }

    public function getErrorMsg(): string
    {
        if (property_exists($this, 'msg'))
            return $this->msg;

        return 'unknown_error';
    }

    public function getErrorCode(): int
    {
        if (property_exists($this, 'code'))
            return $this->code;

        return 0;
    }

    /**
     * @param Response $response
     * @return static|BaseDTO|null
     */
    public static function fromHttpResponse(Response $response): ?BaseDTO
    {
        return static::fromArray($response->json());
    }

    /**
     * @param $array
     * @return static|BaseDTO|null
     */
    public static function fromArray($array): ?BaseDTO
    {
        if ($array === null)
            return null;

        $self = new static();

        foreach ($array as $key => $value) {
            if (property_exists($self, $key))
                $self->$key = $value;
        }

        return $self;
    }

    /**
     * @param Request $request
     * @return static|BaseDTO|null
     */
    public static function fromJsonRequest(Request $request): ?BaseDTO
    {
        $contentString = $request->getContent();

        $content = json_decode($contentString, true);

        if ($content === null)
            return new static();

        return static::fromArray($content);
    }

    /**
     * List all attributes as array
     *
     * @return array
     */
    public function all(): array
    {
        return get_object_vars($this);
    }
}