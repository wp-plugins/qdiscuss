<?php namespace Qdiscuss\Core\Events;

use Qdiscuss\Core\Models\User;

class AvatarWillBeUploaded
{
    public $user;

    public $command;

    public function __construct(User $user, $command)
    {
        $this->user = $user;
        $this->command = $command;
    }
}
