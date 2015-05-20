<?php namespace Qdiscuss\Extend;

use Illuminate\Foundation\Application;

class ForumAssets implements ExtenderInterface
{
	protected $files;

	public function __construct($files)
	{
		$this->files = $files;
	}

	public function extend()
	{
		global $qdiscuss_event;
		$qdiscuss_event->listen('Qdiscuss\Forum\Events\RenderView', function ($event) {
			$event->assets->addFile($this->files);
		});
	}

}