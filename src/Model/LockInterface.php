<?php

namespace Michcald\LockIt\Model;

interface LockInterface
{
    public function setResourceName($resourceName);

    public function getResourceName();

    public function setType($type);

    public function getType();

    public function setToken($token);

    public function getToken();
}
