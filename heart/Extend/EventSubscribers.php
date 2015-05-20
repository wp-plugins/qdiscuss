<?php namespace Qdiscuss\Extend;

use Illuminate\Foundation\Application;

class EventSubscribers implements ExtenderInterface
{
	protected $subscribers;
	
	public function __construct($subscribers)
	{
		$this->subscribers = $subscribers;
	}

	public function extend()
	{
		global $qdiscuss_event;
		foreach ((array) $this->subscribers as $subscriber) {
			$qdiscuss_event->subscribe($subscriber);
		}
	}
}