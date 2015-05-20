<?php namespace Qdiscuss\Core\Handlers\Commands;

use Qdiscuss\Core\Repositories\DiscussionRepositoryInterface as DiscussionRepository;
use Qdiscuss\Core\Events\DiscussionWillBeSaved;
use Qdiscuss\Core\Support\DispatchesEvents;

class EditDiscussionCommandHandler
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

		$discussion->assertCan($user, 'edit');

		if (isset($command->data['title'])) {
			 $discussion->rename($command->data['title'], $user);
		}

		event(new DiscussionWillBeSaved($discussion, $command));

		$discussion->save();
		$this->dispatchEventsFor($discussion);

		return $discussion;
	}
}
