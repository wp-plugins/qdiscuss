<?php namespace Qdiscuss;

use Illuminate\Contracts\Events\Dispatcher;
use Qdiscuss\Core\Models\Notification;
use Qdiscuss\Core\Models\User;
use Qdiscuss\Core\Models\Post;
use Qdiscuss\Core\Models\Permission;
use Closure;

class App
{

	public function __construct()
	{
		// global $qdiscuss_app, $qdiscuss_event;
		// $this->app = $qdiscuss_app;
		// $this->event = $qdiscuss_event;
	}

	/**
	 * Register the service provider.
	*
	* @return void
	*/
	public function register()
	{
	//
	}
	
	public function extend()
	{
		foreach (func_get_args() as $extender) {
			$extender->extend();
		}
	}

	// protected function forumAssets($assets)
	// {
	// 	$this->event->listen('Qdiscuss\Forum\Events\RenderView', function ($event) use ($assets) {
	// 		$event->assets->addFile($assets);
	// 	});
	// }

	// protected function postType($class)
	// {
	// 	Post::addType($class);
	// }

	// protected function discussionGambit($class)
	// {
	// 	$this->event->listen('Qdiscuss\Core\Events\RegisterDiscussionGambits', function ($event) use ($class) {
	// 		$event->gambits->add($class);
	// 	});
	// }

	// protected function formatter($name, $class, $priority = 0)
	// {
	//         $this->app['qdiscuss.formatter']->add($name, $class, $priority);
	// }

	// protected function notificationType($class, $defaultPreferences = [])
	// {
	// 	$notifier = $this->app['qdiscuss.notifier'];

	// 	$notifier->registerType($class);

	// 	Notification::registerType($class);

	// 	foreach ($notifier->getMethods() as $method => $sender) {
	// 		if ($sender::compatibleWith($class)) {
	// 			User::registerPreference(User::notificationPreferenceKey($class::getType(), $method), 'boolval', array_get($defaultPreferences, $method, false));
	// 		}
	// 	}
	// }

	// protected function relationship($parent, $type, $name, $child = null)
	// {
	// 	$parent::addRelationship($name, function ($model) use ($type, $name, $child) {
	// 		if ($type instanceof Closure) {
	// 			return $type($model);
	// 		} elseif ($type === 'belongsTo') {
	// 			return $model->belongsTo($child, null, null, $name);
	// 		} else {
	// 			// @todo
	// 		}
	// 	});
	// }

	// protected function serializeRelationship($parent, $type, $name, $child = null)
	// {
	// 	$parent::addRelationship($name, function ($serializer) use ($type, $name, $child) {
	// 		if ($type instanceof Closure) {
	// 			return $type();
	// 		} else {
	// 			return $serializer->$type($child, $name);
	// 		}
	// 	});
	// }

	// protected function serializeAttributes($serializer, Closure $callback)
	// {
	// 	$this->event->listen('Qdiscuss\Api\Events\SerializeAttributes', function ($event) use ($serializer, $callback) {
	// 		if ($event->serializer instanceof $serializer) {
	// 			$callback($event->attributes, $event->model, $event->serializer);
	// 		}
	// 	});
	// }

	// protected function permission($permission)
	// {
	//         Permission::addPermission($permission);
	// }
}
