<?php namespace Qdiscuss\Core\Notifications\Senders;

use Qdiscuss\Core\Notifications\Types\Notification;
use Qdiscuss\Core\Models\Notification as NotificationModel;
use ReflectionClass;

class NotificationAlerter implements NotificationSender
{
    public function send(Notification $notification)
    {
        $model = NotificationModel::alert(
            $notification->getRecipient()->id,
            $notification::getType(),
            $notification->getSender()->id,
            $notification->getSubject()->id,
            $notification->getAlertData()
        );
        $model->save();
    }

    public function compatibleWith($className)
    {
        return (new ReflectionClass($className))->implementsInterface('Qdiscuss\Core\Notifications\Types\AlertableNotification');
    }
}
