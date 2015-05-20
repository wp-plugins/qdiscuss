<?php namespace Qdiscuss\Core\Handlers\Events;

use Qdiscuss\Core\Models\Post;
use Qdiscuss\Core\Events\PostWasPosted;
use Qdiscuss\Core\Events\PostWasDeleted;
use Qdiscuss\Core\Events\PostWasHidden;
use Qdiscuss\Core\Events\PostWasRestored;
use Illuminate\Contracts\Events\Dispatcher;

class DiscussionMetadataUpdater
{
	/**
	 * Register the listeners for the subscriber.
	 *
	 * @param \Illuminate\Contracts\Events\Dispatcher $events
	 */
	public function subscribe(Dispatcher $events)
	{
		$events->listen('Qdiscuss\Core\Events\PostWasPosted', __CLASS__.'@whenPostWasPosted');
		$events->listen('Qdiscuss\Core\Events\PostWasDeleted', __CLASS__.'@whenPostWasDeleted');
		$events->listen('Qdiscuss\Core\Events\PostWasHidden', __CLASS__.'@whenPostWasHidden');
		$events->listen('Qdiscuss\Core\Events\PostWasRestored', __CLASS__.'@whenPostWasRestored');
	}

	public function whenPostWasPosted(PostWasPosted $event)
	{
		$discussion = $event->post->discussion;

		$discussion->comments_count++;
		$discussion->setLastPost($event->post);
		$discussion->save();
	}

	public function whenPostWasDeleted(PostWasDeleted $event)
	{
		$this->removePost($event->post);
	}

	public function whenPostWasHidden(PostWasHidden $event)
	{
		$this->removePost($event->post);
	}

	public function whenPostWasRestored(PostWasRestored $event)
	{
		$discussion = $event->post->discussion;

		$discussion->refreshCommentsCount();
		$discussion->refreshLastPost();
		$discussion->save();
	}

	protected function removePost(Post $post)
	{
		$discussion = $post->discussion;

		$discussion->refreshCommentsCount();

		if ($discussion->last_post_id == $post->id) {
			$discussion->refreshLastPost();
		}

		$discussion->save();
	}
}
