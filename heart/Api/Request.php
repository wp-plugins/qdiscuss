<?php namespace Qdiscuss\Api;

use Qdiscuss\Core\Support\Actor;

class Request
{
	public $input;
	
	public $actor;

	public $httpRequest;
	
	public function __construct(array $input, Actor $actor, $httpRequest = null)
	{
		$this->input = $input;
		$this->actor = $actor;
		$this->http = $httpRequest;
	}
	
	public function get($key, $default = null)
	{
		return array_get($this->input, $key, $default);
	}

	public function all()
	{
		return $this->input;
	}

}
