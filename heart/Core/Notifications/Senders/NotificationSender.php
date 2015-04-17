<?php namespace Qdiscuss\Core\Notifications\Senders;

use Qdiscuss\Core\Notifications\Types\Notification;

interface NotificationSender
{
    public function send(Notification $notification);

    public function compatibleWith($class);
}
