<?php namespace Qdiscuss\Core\Events;

use Qdiscuss\Core\Models\User;

class UserEmailWasChanged
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
