<?php namespace Qdiscuss\Core\Notifications\Senders;

use Qdiscuss\Core\Notifications\Types\Notification;

interface RetractableSender
{
	public function retract(Notification $notification);
}