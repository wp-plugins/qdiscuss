<?php namespace Qdiscuss\Core\Handlers\Events;

use Qdiscuss\Core\Activity\ActivitySyncer;
use Qdiscuss\Core\Activity\PostedActivity;
use Qdiscuss\Core\Activity\StartedDiscussionActivity;
use Qdiscuss\Core\Activity\JoinedActivity;
use Qdiscuss\Core\Events\PostWasPosted;
use Qdiscuss\Core\Events\PostWasDeleted;
use Qdiscuss\Core\Events\PostWasHidden;
use Qdiscuss\Core\Events\PostWasRestored;
use Qdiscuss\Core\Events\UserWasRegistered;
use Qdiscuss\Core\Models\Post;
use Illuminate\Contracts\Events\Dispatcher;

class UserActivitySyncer
{
	protected $activity;
	public function __construct(ActivitySyncer $activity)
	{
		$this->activity = $activity;
	}
	public function subscribe(Dispatcher $events)
	{
		$events->listen('Qdiscuss\Core\Events\PostWasPosted', __CLASS__.'@whenPostWasPosted');
		$events->listen('Qdiscuss\Core\Events\PostWasHidden', __CLASS__.'@whenPostWasHidden');
		$events->listen('Qdiscuss\Core\Events\PostWasRestored', __CLASS__.'@whenPostWasRestored');
		$events->listen('Qdiscuss\Core\Events\PostWasDeleted', __CLASS__.'@whenPostWasDeleted');
		$events->listen('Qdiscuss\Core\Events\UserWasRegistered', __CLASS__.'@whenUserWasRegistered');
	}
	public function whenPostWasPosted(PostWasPosted $event)
	{
		$this->postBecameVisible($event->post);
	}
	public function whenPostWasHidden(PostWasHidden $event)
	{
		$this->postBecameInvisible($event->post);
	}
	public function whenPostWasRestored(PostWasRestored $event)
	{
		$this->postBecameVisible($event->post);
	}
	public function whenPostWasDeleted(PostWasDeleted $event)
	{
		$this->postBecameInvisible($event->post);
	}
	public function whenUserWasRegistered(UserWasRegistered $event)
	{
		$this->activity->sync(new JoinedActivity($event->user), [$event->user]);
	}
	protected function postBecameVisible(Post $post)
	{
		$activity = $this->postedActivity($post);
		$this->activity->sync($activity, [$post->user]);
	}
	protected function postBecameInvisible(Post $post)
	{
		$activity = $this->postedActivity($post);
		$this->activity->sync($activity, []);
	}
	protected function postedActivity(Post $post)
	{
		return $post->number === 1 ? new StartedDiscussionActivity($post) : new PostedActivity($post);
	}
}