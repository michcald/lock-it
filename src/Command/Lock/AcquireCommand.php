<?php

namespace Michcald\LockIt\Command\Lock;

use Michcald\LockIt\Command\AbstractCommand;
use Michcald\LockIt\Command\Traits;

class AcquireCommand extends AbstractCommand
{
    use Traits\ManagerTrait;

    use Traits\LockTrait;

    use Traits\QuorumTrait;

    public function execute()
    {
        $manager = $this->manager;

        if ($manager->currentLockAlreadyExists($this->lock)) {
            return true;
        }

        if (!$manager->canAcquireLock($this->lock)) {
            return false;
        }

        $retry = $manager->getRetryCount();

        $key = $manager->getKeyGenerator()->generate($this->lock);

        do {
            $n = 0;
            $startTime = microtime(true) * 1000;

            foreach ($manager->getAdapters() as $adapter) {
                if ($adapter->set($key, $this->lock->getToken(), $manager->getTtl())) {
                    $n++;
                }
            }

            # Add 2 milliseconds to the drift to account for Redis expires
            # precision, which is 1 millisecond, plus 1 millisecond min drift
            # for small TTLs.
            $drift = $manager->getClockDrift();
            $validityTime = $manager->getTtl() - (microtime(true) * 1000 - $startTime) - $drift;

            if ($manager->getQuorum()->isApproved($n) && $validityTime > 0) {
                return true;
            } else {
                $i = 0;
                foreach ($manager->getAdapters() as $adapter) {
                    $adapter->del($key);
                }
            }
            // Wait a random delay before to retry
            $delay = mt_rand(floor($manager->getRetryMaxDelay() / 2), $manager->getRetryMaxDelay());
            usleep($delay * 1000);
            $retry--;
        } while ($retry > 0);
        return false;
    }
}
