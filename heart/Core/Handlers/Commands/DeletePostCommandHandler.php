<?php namespace Qdiscuss\Core\Handlers\Commands;

use Qdiscuss\Core\Repositories\PostRepositoryInterface as PostRepository;
use Qdiscuss\Core\Events\PostWillBeDeleted;
use Qdiscuss\Core\Support\DispatchesEvents;

class DeletePostCommandHandler
{
    use DispatchesEvents;

    protected $posts;

    public function __construct(PostRepository $posts)
    {
        $this->posts = $posts;
    }

    public function handle($command)
    {
        $user = $command->user;
        $post = $this->posts->findOrFail($command->postId, $user);

        $post->assertCan($user, 'delete');

        event(new PostWillBeDeleted($post, $command));

        $post->delete();
        $this->dispatchEventsFor($post);

        return $post;
    }
}
