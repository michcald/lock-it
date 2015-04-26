<?php

namespace Michcald\LockIt\Command\Traits;

use Michcald\LockIt\Quorum\QuorumInterface;

trait QuorumTrait
{
    /**
     * @var \Michcald\LockIt\Quorum\QuorumInterface
     */
    protected $quorum;

    public function setQuorum(QuorumInterface $quorum)
    {
        $this->quorum = $quorum;

        return $this;
    }

    public function getQuorum()
    {
        return $this->quorum;
    }
}
