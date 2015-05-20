<?php namespace Qdiscuss\Extend;
use Illuminate\Foundation\Application;
use Closure;

class SerializeAttributes implements ExtenderInterface
{

	protected $serializer;

	protected $callback;
	
	public function __construct($serializer, $callback)
	{
		$this->serializer = $serializer;
		$this->callback = $callback;
	}
	
	public function extend()
	{
		// @todo for that neychang not working
		global $qdiscuss_event;

		$qdiscuss_event->listen('Qdiscuss\Api\Events\SerializeAttributes', function ($event) {
			if ($event->serializer instanceof $this->serializer) {
				call_user_func_array($this->callback, [&$event->attributes, $event->model, $event->serializer]);
			}
		});
	}

}