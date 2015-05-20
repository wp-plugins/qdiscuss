<?php namespace Qdiscuss\Extend;

use Illuminate\Foundation\Application;
use Qdiscuss\Core\Models\Notification;
use Qdiscuss\Core\Models\User;

class NotificationType implements ExtenderInterface
{
	protected $class;
	protected $enabled = [];
	public function __construct($class)
	{
		$this->class = $class;
	}
	public function enableByDefault($method)
	{
		$this->enabled[] = $method;
		return $this;
	}
	public function extend()
	{
		global $qdiscuss_app;
		$notifier = $qdiscuss_app['qdiscuss.notifier'];
		$class = $this->class;
		$notifier->registerType($class);
		Notification::registerType($class);
		foreach ($notifier->getMethods() as $method => $sender) {
			if ($sender::compatibleWith($class)) {
				User::registerPreference(User::notificationPreferenceKey($class::getType(), $method), 'boolval', in_array($method, $this->enabled));
			}
		}
	}
}