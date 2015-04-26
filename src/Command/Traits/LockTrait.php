<?php

namespace Michcald\LockIt\Command\Traits;

use Michcald\LockIt\Model\LockInterface;

trait LockTrait
{
    /**
     * @var \Michcald\LockIt\Model\Lock
     */
    protected $lock;

    public function setLock(LockInterface $lock)
    {
        $this->lock = $lock;

        return $this;
    }

    public function getLock()
    {
        return $this->lock;
    }
}
