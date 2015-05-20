<?php namespace Qdiscuss\Api;

use Qdiscuss\Core\Support\Actor;

class Request
{
	public $input;
	
	public $actor;

	public $httpRequest;
	
	public function __construct(array $input, Actor $actor, $httpRequest = null)
	{
		global $qdiscuss_actor;
		$this->input = $input;
		$this->actor = $qdiscuss_actor;
		$this->http = $httpRequest;
	}
	
	public function get($key, $default = null)
	{
		return isset($this->input[$key]) ? $this->input[$key] : $default;
	}

	public function all()
	{
		return $this->input;
	}

}
