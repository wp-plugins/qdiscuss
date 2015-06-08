<?php namespace Qdiscuss\Extend;

use Closure;
use Qdiscuss\Application;

class SerializeAttributes implements ExtenderInterface
{

	protected $serializer;

	protected $callback;
	
	public function __construct($serializer, $callback)
	{
		$this->serializer = $serializer;
		$this->callback = $callback;
	}
	
	public function extend(Application $app)
	{
		$app['events']->listen('Qdiscuss\Api\Events\SerializeAttributes', function ($event) {
			if ($event->serializer instanceof $this->serializer) {
				call_user_func_array($this->callback, [&$event->attributes, $event->model, $event->serializer]);
			}
		});
	}

}