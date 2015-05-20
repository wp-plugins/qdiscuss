<?php namespace Qdiscuss\Extend;

use Illuminate\Foundation\Application;
use Qdiscuss\Core\Models\Post;

class PostType implements ExtenderInterface
{
	protected $class;

	public function __construct($class)
	{
		$this->class = $class;
	}

	public function extend()
	{
		Post::addType($this->class);
	}
}