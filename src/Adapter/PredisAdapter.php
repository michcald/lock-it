<?php

namespace Michcald\LockIt\Adapter;

use Predis\Client as PredisClient;

class PredisAdapter implements AdapterInterface
{
    private $predis;

    public function __construct(PredisClient $predis)
    {
        $this->predis = $predis;
    }

    public function del($key)
    {
        return $this
            ->predis
            ->del($key)
        ;
    }

    public function exists($key)
    {
        return $this
            ->predis
            ->exists($key)
        ;
    }

    public function get($key)
    {
        return $this
            ->predis
            ->get($key)
        ;
    }

    public function keys()
    {
        throw new \Exception('todo');
        // @todo
    }

    public function scan($prefix)
    {
        throw new \Exception('todo');
        // @todo
    }

    public function set($key, $value, $ttl)
    {
        throw new \Exception('todo');
        // @todo set the ttl

        return $this
            ->predis
            ->set($key, $value)
        ;
    }
}
