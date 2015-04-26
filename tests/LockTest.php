<?php

use Michcald\LockIt\LockManager;
use Michcald\LockIt\Quorum\HalfPlusOneQuorum;
use Michcald\LockIt\Model\LockType;
use Michcald\LockIt\Model\Lock;

class LockTesta extends \PHPUnit_Framework_TestCase
{
    public function aaaatestExclusive()
    {
        $ttl = 3600;
        $retryCount = 3;
        $retryMaxDelay = 1;

        $resourceName = 'printer';
        $token = uniqid();

        $adapters = array(
            $adapter = new \Michcald\LockIt\Adapter\MemoryAdapter()
        );

        $quorum = new HalfPlusOneQuorum();
        $quorum->setTotal(count($adapters));

        $lockManager = new LockManager($quorum, $ttl, $retryCount, $retryMaxDelay);
        $lockManager->addAdapter($adapter);

        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::EXCLUSIVE, $token)
        );
        $this->assertTrue($result);
    }

    public function aaatestOther()
    {
        $adapters = array(
            $adapter1 = new \Michcald\LockIt\Adapter\MemoryAdapter(),
            $adapter2 = new \Michcald\LockIt\Adapter\MemoryAdapter(),
            $adapter3 = new \Michcald\LockIt\Adapter\MemoryAdapter(),
            $adapter4 = new \Michcald\LockIt\Adapter\MemoryAdapter(),
            $adapter5 = new \Michcald\LockIt\Adapter\MemoryAdapter(),
        );

        $quorum = new HalfPlusOneQuorum();
        $quorum->setTotal(count($adapters));
        $ttl = 3600;
        $retryCount = 3;
        $retryMaxDelay = 1;

        $resourceName = 'printer';
        $token = uniqid();

        $lockManager = new LockManager($quorum, $ttl, $retryCount, $retryMaxDelay);
        foreach ($adapters as $adapter) {
            $lockManager->addAdapter($adapter);
        }

        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::NULL, $token)
        );
        $this->assertTrue($result);

        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::CONCURRENT_READ, $token)
        );
        $this->assertTrue($result);

        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::CONCURRENT_READ, $token)
        );
        $this->assertTrue($result);

        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::PROTECTED_READ, $token)
        );
        $this->assertTrue($result);

        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::EXCLUSIVE, $token)
        );
        $this->assertFalse($result);

        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::PROTECTED_WRITE, $token)
        );
        $this->assertFalse($result);

        //

        $result = $lockManager->lockExists(
            new Lock($resourceName, LockType::PROTECTED_READ)
        );
        $this->assertTrue($result);

        // simulating that more than the quorum in the adapters has some expired fields
        $lock = new Lock($resourceName, LockType::PROTECTED_READ, $token);
        $key = $lock->getKey();
        $adapter1->del($key);
        $adapter2->del($key);
        $adapter3->del($key);

        $result = $lockManager->lockExists(
            new Lock($resourceName, LockType::PROTECTED_READ)
        );

        $this->assertFalse($result);
    }

    public function aaaatestOther2()
    {
        $adapters = array(
            $adapter1 = new \Michcald\LockIt\Adapter\MemoryAdapter(),
            $adapter2 = new \Michcald\LockIt\Adapter\MemoryAdapter(),
            $adapter3 = new \Michcald\LockIt\Adapter\MemoryAdapter(),
            $adapter4 = new \Michcald\LockIt\Adapter\MemoryAdapter(),
            $adapter5 = new \Michcald\LockIt\Adapter\MemoryAdapter(),
        );

        $quorum = new HalfPlusOneQuorum();
        $quorum->setTotal(count($adapters));

        $ttl = 3600;
        $retryCount = 3;
        $retryMaxDelay = 1;

        $resourceName = 'printer';
        $token = uniqid();

        $lockManager = new LockManager($quorum, $ttl, $retryCount, $retryMaxDelay);
        foreach ($adapters as $adapter) {
            $lockManager->addAdapter($adapter);
        }

        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::EXCLUSIVE, $token)
        );
        $this->assertTrue($result);

        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::PROTECTED_WRITE, $token)
        );
        $this->assertFalse($result);

        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::PROTECTED_READ, $token)
        );
        $this->assertFalse($result);

        // check this out
        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::PROTECTED_WRITE, $token . '123')
        );
        $this->assertFalse($result);

        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::CONCURRENT_WRITE, $token)
        );
        $this->assertFalse($result);

        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::CONCURRENT_WRITE, $token . '1234')
        );
        $this->assertFalse($result);

        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::CONCURRENT_READ, $token)
        );
        $this->assertFalse($result);

        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::CONCURRENT_READ, $token . '12345')
        );
        $this->assertFalse($result);

        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::NULL, $token)
        );
        $this->assertTrue($result);

        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::NULL, $token . '123456')
        );
        $this->assertTrue($result);

        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::EXCLUSIVE, $token)
        );
        $this->assertTrue($result);

        $result = $lockManager->aquireLock(
            new Lock($resourceName, LockType::CONCURRENT_WRITE, $token . 'ab')
        );
        $this->assertTrue($result);
    }

    public function testFailingAdapters()
    {
    }
}
