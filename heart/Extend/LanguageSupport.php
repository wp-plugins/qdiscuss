<?php namespace Qdiscuss\Extend;

use Qdiscuss\Application;

class LanguageSupport
{
	public function __construct($file)
	{
		$this->file = $file;
	}

	public function extend(Application $app)
	{
		$app['events']->listen('Qdiscuss\Forum\Events\LanguageSupport', function ($event) {
			$event->language->addLanguageFile($this->file);
		});
	}

}