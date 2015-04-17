<?php namespace Qdiscuss\Core\Events;

use Qdiscuss\Core\Models\DiscussionState;

class DiscussionWasRead
{
    public $state;

    public function __construct(DiscussionState $state)
    {
        $this->state = $state;
    }
}
