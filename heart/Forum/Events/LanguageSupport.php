<?php namespace Qdiscuss\Forum\Events;

class LanguageSupport
{
	public $languageManager;

	public function __construct($languageManager)
	{
		$this->language = $languageManager;
	}
}