<?php namespace Qdiscuss\Extend;

use Qdiscuss\Application;
use Qdiscuss\Core\Models\Notification;
use Qdiscuss\Core\Models\User;
use Qdiscuss\Api\Serializers\NotificationSerializer;

class NotificationType implements ExtenderInterface
{
	protected $class;
	
	protected $serializer;
	
	protected $enabled = [];

	public function __construct($class, $serializer)
	{
		$this->class = $class;
		$this->serializer = $serializer;
	}
	
	public function enableByDefault($method)
	{
		$this->enabled[] = $method;
		return $this;
	}
	
	public function extend(Application $app)
	{
		$class = $this->class;
		Notification::registerType($class);
		User::registerPreference(User::notificationPreferenceKey($class::getType(), 'alert'), 'boolval', in_array('alert', $this->enabled));
		if ($class::isEmailable()) {
			User::registerPreference(User::notificationPreferenceKey($class::getType(), 'email'), 'boolval', in_array('email', $this->enabled));
		}
		NotificationSerializer::$subjects[$class::getType()] = $this->serializer;
	}
	
}