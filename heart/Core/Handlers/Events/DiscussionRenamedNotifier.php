<?php namespace Qdiscuss\Core\Handlers\Events;

use Qdiscuss\Core\Events\DiscussionWasRenamed;
use Qdiscuss\Core\Models\DiscussionRenamedPost;
use Qdiscuss\Core\Notifications\Types\DiscussionRenamedNotification;
use Qdiscuss\Core\Notifications\Notifier;
use Illuminate\Contracts\Events\Dispatcher;

class DiscussionRenamedNotifier
{
    public function __construct(Notifier $notifier)
    {
        $this->notifier = $notifier;
    }

    /**
     * Register the listeners for the subscriber.
     *
     *  @param \Illuminate\Contracts\Events\Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen('Qdiscuss\Events\DiscussionWasRenamed', __CLASS__.'@whenDiscussionWasRenamed');
    }

    public function whenDiscussionWasRenamed(DiscussionWasRenamed $event)
    {
        $post = $this->createPost($event);

        $post = $event->discussion->addPost($post);

        if ($event->discussion->start_user_id !== $event->user->id) {
           $notification = new DiscussionRenamedNotification($event->discussion, $post->user, $post);
        if ($post->exists) {
              $this->notifier->send($notification, [$post->discussion->startUser]);
        } else {
              $this->notifier->retract($notification);
        }
    }
 }

    protected function createPost(DiscussionWasRenamed $event)
    {
        return DiscussionRenamedPost::reply(
            $event->discussion->id,
            $event->user->id,
            $event->oldTitle,
            $event->discussion->title
        );

    }
}
