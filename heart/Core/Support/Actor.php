<?php namespace Qdiscuss\Core\Support;

use Qdiscuss\Core\Models\Guest;

class Actor
{
    protected static $user;

    public function getUser()
    {
        return $this::$user ?: new Guest;
    }

    public function setUser($user)
    {
        $this::$user = $user;
    }

    public function isAuthenticated()
    {
        return (bool) $this::$user;
    }
}
