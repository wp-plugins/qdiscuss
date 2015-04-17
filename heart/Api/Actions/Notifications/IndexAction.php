<?php namespace Qdiscuss\Api\Actions\Notifications;

use Qdiscuss\Core\Repositories\NotificationRepositoryInterface;
use Qdiscuss\Core\Exceptions\PermissionDeniedException;
use Qdiscuss\Core\Actions\BaseAction;
use Qdiscuss\Api\Serializers\NotificationSerializer;

class IndexAction extends BaseAction
{
    /**
     * Instantiate the action.
     *
     * @param  \Qdiscuss\\Search\Discussions\UserSearcher  $searcher
     */
    public function __construct(NotificationRepositoryInterface $notifications)
    {
        global $qdiscuss_params, $qdiscuss_actor;
        $this->actor = $qdiscuss_actor;
        $this->params = $qdiscuss_params;
        $this->notifications = $notifications;
    }

    /**
     * Show a user's notifications feed.
     *
     * @return \Illuminate\Http\Response
     */
    public function run()
    {
        $params = $this->params;
        $start = $params->start();
        $count = $params->count(10, 50);

        if (! $this->actor->isAuthenticated()) {
            throw new PermissionDeniedException;
        }

        $user = $this->actor->getUser();

        $notifications = $this->notifications->findByUser($user->id, $count, $start);

        $user->markNotificationsAsRead()->save();

        $serializer = new NotificationSerializer(['sender', 'subject', 'subject.discussion']);
        $document = $this->document()->setData($serializer->collection($notifications));

        echo $this->respondWithDocument($document);exit();
    }
}
