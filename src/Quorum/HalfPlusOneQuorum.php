<?php

namespace Michcald\LockIt\Quorum;

use Michcald\LockIt\Quorum\QuorumInterface;

class HalfPlusOneQuorum implements QuorumInterface
{
    private $total;

    public function setTotal($total)
    {
        $this->total = (int) $total;

        return $this;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function getQuorum()
    {
        return min($this->total, ($this->total / 2 + 1));
    }

    public function isApproved($count)
    {
        return $count >= $this->getQuorum();
    }
}
