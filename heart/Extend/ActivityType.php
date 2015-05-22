<?php namespace Qdiscuss\Extend;

use Illuminate\Foundation\Application;
use Qdiscuss\Core\Models\Activity;
use Qdiscuss\Api\Serializers\ActivitySerializer;

class ActivityType implements ExtenderInterface
{
	protected $class;
	protected $serializer;
	public function __construct($class, $serializer)
	{
		$this->class = $class;
		$this->serializer = $serializer;
	}
	public function extend()
	{
		$class = $this->class;
		Activity::registerType($class);
		ActivitySerializer::$subjects[$class::getType()] = $this->serializer;
	}

}