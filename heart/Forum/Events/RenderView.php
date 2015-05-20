<?php namespace Qdiscuss\Forum\Events;

class RenderView
{
	public $data;

	public $assets;

	public $actor;

	public function __construct(&$data, $assets, $actor)
	{
		$this->data = &$data;
		$this->assets = $assets;
		$this->actor = $actor;
	}
}
