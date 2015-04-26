<?php

namespace Michcald\LockIt\KeyGenerator;

use Michcald\LockIt\Model\LockInterface;

interface KeyGeneratorInterface
{
    public function generate(LockInterface $lock);
}
