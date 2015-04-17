<?php namespace Qdiscuss\Api\Actions\Notifications;

use Qdiscuss\Core\Commands\ReadNotificationCommand;
use Qdiscuss\Core\Actions\BaseAction;
use Qdiscuss\Api\Serializers\NotificationSerializer;

class UpdateAction extends BaseAction
{
    public function __construct()
    {
        global $qdiscuss_actor;
        $this->actor = $qdiscuss_actor;
    }

    /**
     * Edit a discussion. Allows renaming the discussion, and updating its read
     * state with regards to the current user.
     *
     * @param  int $id
     * @return  Json
     */
    public  function run($id)
    {
        $notificationId = $id;
        $user = $this->actor->getUser();
        $params = $this->post_data();

        if ($params->get('notifications.isRead')) {
            $command = new ReadNotificationCommand($notificationId, $user);
            $notification = $this->dispatch($command, $params);
        }

        $serializer = new NotificationSerializer;
        $document = $this->document()->setData($serializer->resource($notification));

        echo  $this->respondWithDocument($document);exit();
    }
}
