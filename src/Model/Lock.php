<?php

namespace Michcald\LockIt\Model;

use Michcald\LockIt\Model\LockInterface;

class Lock implements LockInterface
{
    private $resourceName;

    private $type;

    private $token;

    public function __construct($resourceName, $type, $token = null)
    {
        $this->resourceName = $resourceName;
        $this->type = $type;
        $this->token = $token;
    }

    public function getResourceName()
    {
        return $this->resourceName;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getKey()
    {
        if ($this->token) {
            return sprintf(
                '%s:%s:%s',
                $this->resourceName,
                $this->type,
                $this->token
            );
        }

        return sprintf(
            '%s:%s',
            $this->resourceName,
            $this->type
        );
    }
}
