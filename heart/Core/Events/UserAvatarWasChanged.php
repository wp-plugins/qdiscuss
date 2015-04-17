<?php namespace Qdiscuss\Core\Events;

use Qdiscuss\Core\Models\User;

class UserAvatarWasChanged
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
