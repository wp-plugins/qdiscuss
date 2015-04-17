<?php namespace Qdiscuss\Core\Handlers\Commands;

use Qdiscuss\Core\Models\Notification;
use Qdiscuss\Core\Exceptions\PermissionDeniedException;
use Qdiscuss\Core\Support\DispatchesEvents;

class ReadNotificationCommandHandler
{
    use DispatchesEvents;

    public function handle($command)
    {
        $user = $command->user;

        if (! $user->exists) {
            throw new PermissionDeniedException;
        }

        $notification = Notification::where('user_id', $user->id)->findOrFail($command->notificationId);

        $notification->read();

        $notification->save();
        $this->dispatchEventsFor($notification);

        return $notification;
    }
}
