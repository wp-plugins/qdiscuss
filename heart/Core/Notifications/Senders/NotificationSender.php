<?php namespace Qdiscuss\Core\Notifications\Senders;

use Qdiscuss\Core\Notifications\Types\Notification;
use Qdiscuss\Core\Models\User;

interface NotificationSender
{
	public function send(Notification $notification, User $user);
	public static function compatibleWith($class);
}