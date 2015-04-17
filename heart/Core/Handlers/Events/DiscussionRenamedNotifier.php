<?php namespace Qdiscuss\Core\Handlers\Events;

use Qdiscuss\Core\Events\DiscussionWasRenamed;
use Qdiscuss\Core\Models\DiscussionRenamedPost;
use Qdiscuss\Core\Notifications\Types\DiscussionRenamedNotification;
use Qdiscuss\Core\Notifications\Notifier;

class DiscussionRenamedNotifier
{
    public function __construct(Notifier $notifier)
    {
        $this->notifier = $notifier;
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen('Qdiscuss\Events\DiscussionWasRenamed', __CLASS__.'@whenDiscussionWasRenamed');
    }

    public function whenDiscussionWasRenamed(DiscussionWasRenamed $event)
    {
        $post = $this->createPost($event);

        $event->discussion->postWasAdded($post);

        if ($event->discussion->start_user_id !== $event->user->id) {
            $this->sendNotification($event, $post);
        }
    }

    protected function createPost(DiscussionWasRenamed $event)
    {
        $post = DiscussionRenamedPost::reply(
            $event->discussion->id,
            $event->user->id,
            $event->oldTitle,
            $event->discussion->title
        );

        $post->save();

        return $post;
    }

    protected function sendNotification(DiscussionWasRenamed $event, DiscussionRenamedPost $post)
    {
        $notification = new DiscussionRenamedNotification(
            $event->discussion->startUser,
            $event->user,
            $post,
            $event->oldTitle
        );

        $this->notifier->send($notification);
    }
}
