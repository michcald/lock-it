<?php

namespace Michcald\LockIt\Model;

abstract class LockType
{
    /**
     * Null (NL).
     *
     * Indicates interest in the resource, but does not prevent other processes
     * from locking it. It has the advantage that the resource and its lock
     * value block are preserved, even when no processes are locking it.
     */
    const NULL = 'NL';

    /**
     * Protected Read (PR).
     *
     * This is the traditional share lock, which indicates a desire to read the
     * resource but prevents other from updating it. Others can however also
     * read the resource.
     */
    const PROTECTED_READ = 'PR';

    /**
     * Protected Write (PW).
     *
     * This is the traditional update lock, which indicates a desire to read and
     * update the resource and prevents others from updating it. Others with
     * Concurrent Read access can however read the resource.
     */
    const PROTECTED_WRITE = 'PW';

    /**
     * Concurrent Read (CR).
     *
     * Indicates a desire to read (but not update) the resource. It allows other
     * processes to read or update the resource, but prevents others from having
     * exclusive access to it. This is usually employed on high-level resources,
     * in order that more restrictive locks can be obtained on subordinate
     * resources.
     */
    const CONCURRENT_READ = 'CR';

    /**
     * Concurrent Write (CW).
     *
     * Indicates a desire to read and update the resource. It also allows other
     * processes to read or update the resource, but prevents others from having
     * exclusive access to it. This is also usually employed on high-level
     * resources, in order that more restrictive locks can be obtained on
     * subordinate resources.
     */
    const CONCURRENT_WRITE = 'CW';

    /**
     * Exclusive (EX).
     *
     * This is the traditional exclusive lock which allows read and update
     * access to the resource, and prevents others from having any access to it.
     */
    const EXCLUSIVE = 'EX';

    public static function getAll()
    {
        return array(
            self::NULL,
            self::PROTECTED_READ,
            self::PROTECTED_WRITE,
            self::CONCURRENT_READ,
            self::CONCURRENT_WRITE,
            self::EXCLUSIVE,
        );
    }

    public static function getSimultaneousAllowedLocks($type)
    {
        switch ($type) {
            case self::NULL:
                return array(
                    self::NULL,
                    self::PROTECTED_READ,
                    self::PROTECTED_WRITE,
                    self::CONCURRENT_READ,
                    self::CONCURRENT_WRITE,
                    self::EXCLUSIVE,
                );
            case self::PROTECTED_READ:
                return array(
                    self::NULL,
                    self::CONCURRENT_READ,
                    self::PROTECTED_READ,
                );
            case self::PROTECTED_WRITE:
                return array(
                    self::NULL,
                    self::CONCURRENT_READ,
                );
            case self::CONCURRENT_READ:
                return array(
                    self::NULL,
                    self::PROTECTED_READ,
                    self::PROTECTED_WRITE,
                    self::CONCURRENT_READ,
                    self::CONCURRENT_WRITE,
                );
            case self::CONCURRENT_WRITE:
                return array(
                    self::NULL,
                    self::CONCURRENT_READ,
                    self::CONCURRENT_WRITE,
                );
            case self::EXCLUSIVE:
                return array(
                    self::NULL,
                );
            default:
                throw new InvalidLockTypeException(); // @todo
        }
    }

    public static function getSimultaneousNotAllowedLocks($type)
    {
        $types = array();
        foreach (self::getAll() as $type) {
            if (!in_array($type, self::getSimultaneousAllowedLocks($type))) {
                $types[] = $type;
            }
        }
        return $types;
    }
}
