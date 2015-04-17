<?php namespace Qdiscuss\Core\Handlers\Commands;

use Qdiscuss\Core\Repositories\DiscussionRepositoryInterface as DiscussionRepository;
use Qdiscuss\Core\Events\DiscussionStateWillBeSaved;
use Qdiscuss\Core\Exceptions\PermissionDeniedException;
use Qdiscuss\Core\Support\DispatchesEvents;

class ReadDiscussionCommandHandler
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

        if (! $user->exists) {
            throw new PermissionDeniedException;
        }

        $discussion = $this->discussions->findOrFail($command->discussionId, $user);

        $state = $discussion->stateFor($user);
        $state->read($command->readNumber);

        event(new DiscussionStateWillBeSaved($state, $command));

        $state->save();
        $this->dispatchEventsFor($state);

        return $state;
    }
}
