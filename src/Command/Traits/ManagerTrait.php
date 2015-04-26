<?php

namespace Michcald\LockIt\Command\Traits;

use Michcald\LockIt\Manager\ManagerInterface;

trait ManagerTrait
{
    /**
     * @var \Michcald\LockIt\Manager\ManagerInterface
     */
    protected $manager;

    public function setManager(ManagerInterface $manager)
    {
        $this->manager = $manager;

        return $this;
    }

    public function getManager()
    {
        return $this->manager;
    }
}
