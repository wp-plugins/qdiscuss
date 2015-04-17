<?php namespace Qdiscuss\Forum\Events;

use Qdiscuss\Core\Models\User;

class UserLoggedOut
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
