<?php

use Michcald\LockIt\Manager\LockManager;
use Michcald\LockIt\Quorum\HalfPlusOneQuorum;
use Michcald\LockIt\KeyGenerator\DefaultKeyGenerator;
use Michcald\LockIt\Adapter;

class LockManagerTest extends \PHPUnit_Framework_TestCase
{
    private $manager;

    public function setUp()
    {
        parent::setUp();

        $adapters = array(
            new Adapter\MemoryAdapter(),
            new Adapter\MemoryAdapter(),
            new Adapter\MemoryAdapter(),
            new Adapter\MemoryAdapter(),
            new Adapter\MemoryAdapter(),
        );
        $quorum = new HalfPlusOneQuorum();
        $keyGenerator = new DefaultKeyGenerator();
        $ttl = 3600;
        $retryCount = 3;
        $retryMaxDelay = 1;
        $this->manager = new LockManager(
            $adapters,
            $quorum,
            $keyGenerator,
            $ttl,
            $retryCount,
            $retryMaxDelay
        );
    }

    public function testCurrentLocks()
    {
        $currentLocks = $this
            ->manager
            ->getCurrentLocks()
        ;

        $this->assertCount(0, $currentLocks);
    }

    public function testCanAcquireLock()
    {
        // @todo
    }

    public function testCurrentLockALreadyExists()
    {
        // @todo
    }
}