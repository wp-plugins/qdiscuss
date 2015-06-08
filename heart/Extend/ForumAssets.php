<?php namespace Qdiscuss\Extend;

use Qdiscuss\Application;

class ForumAssets implements ExtenderInterface
{
	protected $files;

	public function __construct($files)
	{
		$this->files = $files;
	}

	public function extend(Application $app)
	{
		$app['events']->listen('Qdiscuss\Forum\Events\RenderView', function ($event) {
			$event->assets->addFile($this->files);
		});
	}

}