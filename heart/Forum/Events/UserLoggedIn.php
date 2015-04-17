<?php namespace Qdiscuss\Forum\Events;

use Qdiscuss\Core\Models\User;

class UserLoggedIn
{
    public $user;

    public $token;

    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }
}
