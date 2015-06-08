<?php namespace Qdiscuss\Extend;

use Qdiscuss\Application;

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
	
	public function extend(Application $app)
	{
		$app['qdiscuss.formatter']->add($this->name, $this->class, $this->priority);
	}

}