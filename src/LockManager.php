<?php

namespace Michcald\LockIt;

use Michcald\LockIt\Adapter\AdapterInterface;
use Michcald\LockIt\Quorum\QuorumInterface;
use Michcald\LockIt\Model\LockInterface;

class LockManager
{
    const CLOCK_DRIFT_FACTOR = 0.01;

    /**
     * @var array[\Michcald\LockIt\Adapter\AdapterInterface]
     */
    private $adapters;

    private $quorum;

    private $ttl;

    private $retryCount;

    private $retryMaxDelay;

    public function __construct(QuorumInterface $quorum, $ttl, $retryCount, $retryMaxDelay)
    {
        $this->adapters = array();
        $this->quorum = $quorum;
        $this->ttl = (int)$ttl; // @todo
        $this->retryCount = (int)$retryCount;
        $this->retryMaxDelay = (int)$retryMaxDelay;
    }

    public function addAdapter(AdapterInterface $adapter)
    {
        $this->adapters[] = $adapter;

        return $this;
    }

    public function getAdapters()
    {
        return $this->adapters;
    }

    public function currentLockAlreadyExists(LockInterface $lock)
    {
        $i = 0;
        foreach ($this->adapters as $adapter) {
            if ($adapter->exists($lock->getKey())) {
                $i ++;
            }
        }
        return $this
            ->quorum
            ->isApproved($i)
        ;
    }

    public function lockExists(LockInterface $lock)
    {
        $i = 0;
        foreach ($this->adapters as $adapter) {
            if (count($adapter->scan($lock->getKey())) > 0) {
                $i ++;
            }
        }
        return $this
            ->quorum
            ->isApproved($i)
        ;
    }

    private function getCurrentLocks()
    {
        $locks = array();
        foreach ($this->adapters as $adapter) {
            foreach ($adapter->keys() as $k) {
                if (isset($locks[$k])) {
                    $locks[$k]['count'] ++;
                } else {
                    $tmp = explode(':', $k); // @todo this logic should not be here
                    $locks[$k] = array(
                        'lock' => new Model\Lock($tmp[0], $tmp[1], $tmp[2]),
                        'count' => 0
                    );
                }
            }
        }

        // considering only the quorum
        $finalLocks = array();
        foreach ($locks as $l) {
            if ($this->quorum->isApproved($l['count'])) {
                $finalLocks[] = $l['lock'];
            }
        }

        return $finalLocks;
    }

    public function canAquireLock(LockInterface $lock)
    {
        foreach ($this->getCurrentLocks() as $l) {
            if (!in_array($lock->getType(), Model\LockType::getSimultaneousAllowedLocks($lock->getType()))) {
                return false;
            }
        }
        return true;
    }

    public function aquireLock(LockInterface $lock)
    {
        if ($this->currentLockAlreadyExists($lock)) {
            return true;
        }

        if (!$this->canAquireLock($lock)) {
            return false;
        }

        $retry = $this->retryCount;

        do {
            $n = 0;
            $startTime = microtime(true) * 1000;

            foreach ($this->adapters as $adapter) {
                if ($adapter->set($lock->getKey(), $lock->getToken(), $this->ttl)) {
                    $n++;
                }
            }

            # Add 2 milliseconds to the drift to account for Redis expires
            # precision, which is 1 millisecond, plus 1 millisecond min drift
            # for small TTLs.
            $drift = ($this->ttl * self::CLOCK_DRIFT_FACTOR) + 2;
            $validityTime = $this->ttl - (microtime(true) * 1000 - $startTime) - $drift;

            if ($this->quorum->isApproved($n) && $validityTime > 0) {
                return true;
            } else {
                $i = 0;
                foreach ($this->adapters as $adapter) {
                    $adapter->del($lock->getKey());
                }
            }
            // Wait a random delay before to retry
            $delay = mt_rand(floor($this->retryMaxDelay / 2), $this->retryMaxDelayDelay);
            usleep($delay * 1000);
            $retry--;
        } while ($retry > 0);
        return false;
    }

    public function releaseLock(LockInterface $lock)
    {
        $i = 0;
        foreach ($this->adapters as $adapter) {
            if ($adapter->del($lock->getKey())) {
                $i ++;
            }
        }
        return $this
            ->quorum
            ->isApproved($i)
        ;
    }
}
