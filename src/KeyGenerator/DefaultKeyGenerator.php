<?php

namespace Michcald\LockIt\KeyGenerator;

use Michcald\LockIt\Model\LockInterface;

class DefaultKeyGenerator
{
    public function generate(LockInterface $lock)
    {
        return sprintf(
            '%s:%s:%s',
            $lock->getResourceName(),
            $lock->getType(),
            $lock->getToken()
        );
    }
}
