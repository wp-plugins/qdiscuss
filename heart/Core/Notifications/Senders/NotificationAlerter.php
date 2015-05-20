<?php namespace Qdiscuss\Core\Notifications\Senders;

use Qdiscuss\Core\Notifications\Types\Notification;
use Qdiscuss\Core\Models\Notification as NotificationModel;
use Qdiscuss\Core\Models\User;
use ReflectionClass;

class NotificationAlerter implements NotificationSender, RetractableSender
{
	public function send(Notification $notification, User $user)
	{
		$model = NotificationModel::alert(
			$user->id,
			$notification::getType(),
			$notification->getSender()->id,
			$notification->getSubject()->id,
			$notification->getAlertData()
		);

		$model->save();
	}

	public function retract(Notification $notification)
	{
		$models = NotificationModel::where('type', $notification::getType())
			->where('subject_id', $notification->getSubject()->id)
			->delete();
	}

	public static function compatibleWith($className)
	{
		return (new ReflectionClass($className))->implementsInterface('Qdiscuss\Core\Notifications\Types\AlertableNotification');
	}
}