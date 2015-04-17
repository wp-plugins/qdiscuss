<?php namespace Qdiscuss\Core\Events;

use Qdiscuss\Core\Models\Discussion;

class DiscussionWasStarted
{
    public $discussion;

    public function __construct(Discussion $discussion)
    {
        $this->discussion = $discussion;
    }
}
