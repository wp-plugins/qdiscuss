<?php namespace Qdiscuss\Core\Handlers\Commands;

use Qdiscuss\Core\Repositories\DiscussionRepositoryInterface as DiscussionRepository;
use Qdiscuss\Core\Events\DiscussionWillBeDeleted;
use Qdiscuss\Core\Support\DispatchesEvents;

class DeleteDiscussionCommandHandler
{
    use DispatchesEvents;

    protected $discussions;

    public function __construct(DiscussionRepository $discussions)
    {
        $this->discussions = $discussions;
    }

    public function handle($command)
    {
        $user = $command->user;
        $discussion = $this->discussions->findOrFail($command->discussionId, $user);

        $discussion->assertCan($user, 'delete');

        event(new DiscussionWillBeDeleted($discussion, $command));

        $discussion->delete();
        $this->dispatchEventsFor($discussion);

        return $discussion;
    }
}
