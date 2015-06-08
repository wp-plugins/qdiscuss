<?php namespace Qdiscuss\Core\Support;

use Qdiscuss\Core;

class LanguageManager
{

	protected $files = [];

	public function __construct()
	{
		$language = Core::config('forum_language', 'en');
		$this->files[] = qd_language_path() . '/' . $language . '.json';			
	}

	public function addLanguageFile($file)
	{
		$this->files[] =  $file;
	}

	public function getLanguageFilesConent()
	{
		$contents = "";

		if ($this->files) {
			foreach ($this->files as $file) {
				if(file_exists($file)) {
					$file_content = str_replace(['{', '}', "\n"], '', file_get_contents($file));
					if (substr($file_content, -1) != ',')  $file_content .= ',';
					$contents .= $file_content;
				}
			}
		}

		$contents = substr($contents, 0, -1);
		$contents = "{ " . $contents .= " }";

		return $contents;
	}

}