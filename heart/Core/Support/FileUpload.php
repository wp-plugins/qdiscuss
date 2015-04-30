<?php namespace Qdiscuss\Core\Support;

class FileUpload {
	
	public function __construct($data)
	{
		$this->data = $data;
	}

	public function getFilename()
	{
		$originalName = $this->getOriginName($this->data['tmp_name']);
		$pos = strrpos($originalName, DIRECTORY_SEPARATOR);
		$originalName = false === $pos ? $originalName : substr($originalName, $pos + 1);
		return $originalName;
	}

	public function getPath()
	{
		$originalName = $this->getOriginName($this->data['tmp_name']);
		$pos = strrpos($originalName, DIRECTORY_SEPARATOR);
		$path = false === $pos ? '' : substr($originalName, 0, $pos);
		return $path;
	}

	protected function getOriginName($name)
	{
		return str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $name);
	}
}