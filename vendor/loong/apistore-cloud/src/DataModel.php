<?php

declare(strict_types=1);

namespace loong\ApiStore;

use ArrayAccess;
use JsonSerializable;

class DataModel implements ArrayAccess, JsonSerializable
{
    protected $origin;
    protected $data;
    public function __construct(mixed $data)
    {
        $this->origin = (array)$data;
        $this->data = (array)$data;
    }
    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }
    public function __unset($name)
    {
        unset($this->data[$name]);
    }
    public function __toString()
    {
        return json_encode($this->data, JSON_UNESCAPED_UNICODE);
    }
    public function offsetGet(mixed $offset)
    {
        return $this->data[$offset] ?? null;
    }
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }
    public function offsetExists(mixed $offset): bool
    {
        return $this->__isset($offset);
    }
    public function offsetUnset(mixed $offset): void
    {
        $this->__unset($offset);
    }
    public function toArray()
    {
        return $this->data;
    }
    public function toJson()
    {
        return json_encode($this->data, JSON_UNESCAPED_UNICODE);
    }
    public function jsonSerialize()
    {
        return $this->data;
    }
}
