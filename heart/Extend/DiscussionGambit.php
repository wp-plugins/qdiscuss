<?php namespace Qdiscuss\Extend;

use Illuminate\Foundation\Application;

class DiscussionGambit implements ExtenderInterface
{
	protected $class;

	public function __construct($class)
	{
		global $qdiscuss_event;
		$this->class = $class;
		$this->event = $qdiscuss_event;
	}
	public function extend()
	{
		$this->event->listen('Qdiscuss\Core\Events\RegisterDiscussionGambits', function ($event) {
			$event->gambits->add($this->class);
		});
	}
}