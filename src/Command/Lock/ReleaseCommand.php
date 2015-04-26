<?php

namespace Michcald\LockIt\Command\Lock;

use Michcald\LockIt\Command\AbstractCommand;

class ReleaseCommand extends AbstractCommand
{
    use Traits\ManagerTrait;

    use Traits\LockTrait;

    use Traits\QuorumTrait;

    public function execute()
    {
        $manager = $this->getManager();

        $i = 0;
        foreach ($manager->getAdapters() as $adapter) {
            if ($adapter->del($this->lock->getKey())) {
                $i ++;
            }
        }
        return $manager
            ->getQuorum()
            ->isApproved($i)
        ;
    }
}
