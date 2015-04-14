<?php

namespace Michcald\LockIt\Adapter;

interface AdapterInterface
{
    public function set($key, $value, $ttl);

    public function del($key);

    public function get($key);

    public function exists($key);

    public function scan($prefix);

    public function keys();
}
