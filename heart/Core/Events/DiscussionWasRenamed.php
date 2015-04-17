<?php namespace Qdiscuss\Core\Events;

use Qdiscuss\Core\Models\Discussion;
use Qdiscuss\Core\Models\User;

class DiscussionWasRenamed
{
    public $discussion;

    public $user;

    public $oldTitle;

    public function __construct(Discussion $discussion, User $user, $oldTitle)
    {
        $this->discussion = $discussion;
        $this->user = $user;
        $this->oldTitle = $oldTitle;
    }
}
