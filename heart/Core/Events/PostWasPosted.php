<?php namespace Qdiscuss\Core\Events;

use Qdiscuss\Core\Models\Post;

class PostWasPosted
{
    public $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }
}
