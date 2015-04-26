<?php

namespace Michcald\LockIt\Adapter;

use Michcald\LockIt\Adapter\AdapterInterface;

class MemoryAdapter implements AdapterInterface
{
    private $data = array();

    public function del($key)
    {
        if ($this->exists($key)) {
            unset($this->data[$key]);
            return true;
        }

        return false;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return true;
    }

    public function get($key)
    {
        if (!$this->exists($key)) {
            return false;
        }

        return $this->data[$key];
    }

    public function setTTL($ttl)
    {
        // @todo
        throw new \Exception('todo');
    }

    public function exists($key)
    {
        return array_key_exists($key, $this->data);
    }

    public function scan($prefix)
    {
        $results = array();
        foreach ($this->data as $key => $value) {
            if (strstr($key, $prefix)) {
                $results[] = $key;
            }
        }
        return $results;
    }

    public function keys()
    {
        return array_keys($this->data);
    }
}
