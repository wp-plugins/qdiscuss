<?php namespace Flarum\Extend;

use Illuminate\Foundation\Application;

class Formatter implements ExtenderInterface
{
	protected $name;
	protected $class;
	protected $priority;
	public function __construct($name, $class, $priority = 0)
	{
		$this->name = $name;
		$this->class = $class;
		$this->priority = $priority;
	}
	public function extend()
	{
		global $qdiscuss_app;
		$qdiscuss_app['qdiscuss.formatter']->add($this->name, $this->class, $this->priority);
	}

}