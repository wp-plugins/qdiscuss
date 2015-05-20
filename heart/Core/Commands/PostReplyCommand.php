<?php namespace Qdiscuss\Core\Commands;

class PostReplyCommand
{
    public $discussionId;

    public $user;

    public $data;

    public function __construct($discussionId, $user, $data)
    {
         $this->discussionId = $discussionId;
         $this->user = $user;
         $this->data = $data;
    }
}
