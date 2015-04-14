<?php

namespace Michcald\LockIt\Quorum;

interface QuorumInterface
{
    public function isApproved($count);

    public function getQuorum();
}
