<?php

namespace Michcald\LockIt\Manager;

use Michcald\LockIt\Adapter\AdapterInterface;
use Michcald\LockIt\KeyGenerator\KeyGeneratorInterface;

abstract class AbstractManager implements ManagerInterface
{
    /**
     * @var array[\Michcald\LockIt\Adapter\AdapterInterface]
     */
    private $adapters;

    private $keyGenerator;

    private $ttl;

    private $retryCount;

    private $retryMaxDelay;

    public function __construct(
        array $adapters,
        QuorumInterface $quorum,
        KeyGeneratorInterface $keyGenerator,
        $ttl,
        $retryCount,
        $retryMaxDelay
    ) {
        $this->adapters = $adapters;
        $this->quorum = $quorum;
        $this->keyGenerator = $keyGenerator;
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

    public function getKeyGenerator()
    {
        return $this->keyGenerator;
    }

    public function getRetryCount()
    {
        return $this->retryCount;
    }

    public function getTtl()
    {
        return $this->ttl;
    }

    public function getRetryMaxDelay()
    {
        return $this->retryMaxDelay;
    }

    public function getCurrentLocks()
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

    public function canAcquireLock(LockInterface $lock)
    {
        foreach ($this->getCurrentLocks() as $l) {
            if (!in_array(
                $lock->getType(),
                Model\LockType::getSimultaneousAllowedLocks($lock->getType())
            )) {
                return false;
            }
        }
        return true;
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

    public function getClockDrift()
    {
        return ($this->ttl * ManagerInterface::CLOCK_DRIFT_FACTOR) + 2;
    }
}
