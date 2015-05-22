<?php namespace Qdiscuss\Core\Handlers\Events;

use Qdiscuss\Core\Events\DiscussionWasRenamed;
use Qdiscuss\Core\Models\DiscussionRenamedPost;
use Qdiscuss\Core\Notifications\DiscussionRenamedNotification;
use Qdiscuss\Core\Notifications\NotificationSyncer;
use Illuminate\Contracts\Events\Dispatcher;

class DiscussionRenamedNotifier
{
	protected $notifications;

	public function __construct(NotificationSyncer $notifications)
	{
		$this->notifications = $notifications;
	}

	/**
	 * Register the listeners for the subscriber.
	 *
	 * @param \Illuminate\Contracts\Events\Dispatcher $events
	 */
	public function subscribe(Dispatcher $events)
	{
		$events->listen('Qdiscuss\Core\Events\DiscussionWasRenamed', __CLASS__.'@whenDiscussionWasRenamed');
	}
	
	public function whenDiscussionWasRenamed(DiscussionWasRenamed $event)
	{
		$post = DiscussionRenamedPost::reply(
			$event->discussion->id,
			$event->user->id,
			$event->oldTitle,
			$event->discussion->title
		);
		$post = $event->discussion->addPost($post);
		if ($event->discussion->start_user_id !== $event->user->id) {
			$notification = new DiscussionRenamedNotification($post);
			$this->notifications->sync($notification, $post->exists ? [$event->discussion->startUser] : []);
		}
	}
	
}