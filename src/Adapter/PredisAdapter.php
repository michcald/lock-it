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

    public function extendLockLife($resourceName, $hash, $ttl)
    {
    }

    public function lock($resourceName, $hash, $ttl, $type = LockType::NL)
    {
        if (!$this->predis->exists($resourceName)) {
            $this
                ->predis
                ->set($resourceName, $hash)
            ;

            $this
                ->predis
                ->expire($resourceName, $ttl)
            ;
        }

        return true;
    }

    public function unlock($resourceName, $hash)
    {
        if ($this->predis->exists($resourceName)) {
            $currentHash = $this
                ->predis
                ->get($resourceName)
            ;

            if ($currentHash == $hash) {
                $this
                    ->predis
                    ->del($resourceName)
                ;
                return true;
            }
        }

        return false;
    }
}
