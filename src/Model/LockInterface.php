<?php

namespace Michcald\LockIt\Model;

interface LockInterface
{
    public function __construct($resourceName, $type, $token = null);

    public function getResourceName();

    public function getType();

    public function getToken();

    public function getKey();
}
