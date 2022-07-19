<?php

namespace App\Entity;

use Illuminate\Support\Collection;

class AbstractEntity
{
    /**
     * @param $dbRes
     * @return static|AbstractEntity|null
     */
    public static function fromDBRes($dbRes): ?AbstractEntity
    {
        if ($dbRes === null)
            return null;

        return static::fromArray((array)$dbRes);
    }

    /**
     * @param Collection $dbRes
     * @return static[]
     */
    public static function arrayFromDBRes(Collection $dbRes): array
    {
        $entryList = [];

        foreach ($dbRes as $entry) {
            $entryList[] = static::fromArray((array)$entry);
        }

        return $entryList;
    }

    /**
     * @param $array
     * @return static|AbstractEntity|null
     */
    public static function fromArray($array): ?AbstractEntity
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
     * @return array
     */
    public function toArray()
    {
        return (array)$this;
    }

    /**
     * @return false|string
     */
    public function toJSON()
    {
        return json_encode($this->toArray());
    }

    /**
     * @param string|null $key
     *
     * @return bool
     */
    public function has(?string $key)
    {
        if ($key === null)
            return false;

        return property_exists($this, $key);
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return static|AbstractEntity
     */
    public function set(string $key, $value)
    {
        if (property_exists($this, $key))
            $this->$key = $value;

        return $this;
    }

    /**
     * @param string $key
     * @param null $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if (property_exists($this, $key))
            return $this->$key;

        return $default;
    }

    /**
     * @param string $key
     *
     * @return static|AbstractEntity
     */
    public function delete(string $key)
    {
        if (property_exists($this, $key))
            unset($this->$key);

        return $this;
    }

    /**
     * @param array $fields
     * @return static|AbstractEntity
     */
    public function deleteFields(array $fields)
    {
        foreach ($fields as $field)
            $this->delete($field);

        return $this;
    }
}