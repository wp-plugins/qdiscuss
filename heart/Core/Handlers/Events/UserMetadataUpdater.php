<?php namespace Qdiscuss\Core\Handlers\Events;

use Qdiscuss\Core\Models\User;
use Qdiscuss\Core\Events\PostWasPosted;
use Qdiscuss\Core\Events\PostWasDeleted;
use Qdiscuss\Core\Events\PostWasHidden;
use Qdiscuss\Core\Events\PostWasRestored;
use Qdiscuss\Core\Events\DiscussionWasStarted;
use Qdiscuss\Core\Events\DiscussionWasDeleted;

class UserMetadataUpdater
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen('Qdiscuss\Core\Events\PostWasPosted', __CLASS__.'@whenPostWasPosted');
        $events->listen('Qdiscuss\Core\Events\PostWasDeleted', __CLASS__.'@whenPostWasDeleted');
        $events->listen('Qdiscuss\Core\Events\PostWasHidden', __CLASS__.'@whenPostWasHidden');
        $events->listen('Qdiscuss\Core\Events\PostWasRestored', __CLASS__.'@whenPostWasRestored');
        $events->listen('Qdiscuss\Core\Events\DiscussionWasStarted', __CLASS__.'@whenDiscussionWasStarted');
        $events->listen('Qdiscuss\Core\Events\DiscussionWasDeleted', __CLASS__.'@whenDiscussionWasDeleted');
    }

    public function whenPostWasPosted(PostWasPosted $event)
    {
        $this->updateRepliesCount($event->post->user, 1);
    }

    public function whenPostWasDeleted(PostWasDeleted $event)
    {
        $this->updateRepliesCount($event->post->user, -1);
    }

    public function whenPostWasHidden(PostWasHidden $event)
    {
        $this->updateRepliesCount($event->post->user, -1);
    }

    public function whenPostWasRestored(PostWasRestored $event)
    {
        $this->updateRepliesCount($event->post->user, 1);
    }

    public function whenDiscussionWasStarted(DiscussionWasStarted $event)
    {
        $this->updateDiscussionsCount($event->discussion->startUser, 1);
    }

    public function whenDiscussionWasDeleted(DiscussionWasDeleted $event)
    {
        $this->updateDiscussionsCount($event->discussion->startUser, -1);
    }

    protected function updateRepliesCount(User $user, $amount)
    {
        $user->comments_count += $amount;
        $user->save();
    }

    protected function updateDiscussionsCount(User $user, $amount)
    {
        $user->discussions_count += $amount;
        $user->save();
    }
}
