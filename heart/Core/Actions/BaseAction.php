<?php namespace Qdiscuss\Core\Actions;

use Tobscure\JsonApi\Document;
use Illuminate\Contracts\Bus\Dispatcher;
use Qdiscuss\Core\Support\Action;
use Qdiscuss\Core\Support\Actor;
use Qdiscuss\Api\Events\CommandWillBeDispatched;
use Qdiscuss\Api\Events\WillRespondWithDocument;
use Qdiscuss\Core\Actions\ApiParams;

abstract class BaseAction extends Action{

	public function __construct(Actor $actor, Dispatcher $bus)
	{
		$this->actor = $actor;
		$this->bus = $bus;
	}

	/**
	 * Get the post datas
	 * 
	 * @return array
	 */
	// protected function post_data()
	// {
	// 	return new ApiParams(json_decode(file_get_contents("php://input"), true));
	// }
	
	public function handle()
	{
		$data = array();

		if($post_data = file_get_contents("php://input")){
			$post_data = json_decode($post_data, true);
			if($_GET){
				$data = array_merge($post_data, $_GET);
			}
			$data = $post_data;
		}else{
			if($_GET){
				$data = $_GET;
			}
		}

		return $this->call($data);

	}

	public function call($params=[])
	{
		$params = new ApiParams($params);

		return $this->run($params);
	}

	public function hydrate($object, $params)
	{
	    foreach ($params as $k => $v) {
	        $object->$k = $v;
	    }
	}


	/**
	 * @param ApiParams $params
	 * @return mixed
	 */
	protected function run()
	{
	    	
	}

	protected function dispatch($command, $params = [])
	{
		global $qdiscuss_bus;
		$this->event(new CommandWillBeDispatched($command, $params));
		return $qdiscuss_bus->dispatch($command);
	
	}

	protected function event($event)
	{
		event($event);
	}

	protected function document()
	{
		return new Document;
	}

	protected function buildUrl($route, $params = [], $input = [])
	{
		// $url = route('flarum.api.'.$route, $params);
		$url = $_SERVER["HTTP_HOST"] . $_SERVER["PATH_INFO"];
		$queryString = $input ? '?'.http_build_query($input) : '';

		return $url.$queryString;
	}

	protected function respondWithoutContent($statusCode = 204, $headers = [])
	{
		// return Response::make('', $statusCode, $headers);
	 	return header("HTTP/1.0 " . $statusCode);
	}

	protected function respondWithArray($array, $statusCode = 200, $headers = [])
	{
		// return Response::json($array, $statusCode, $headers);
		header("Content-type: application/json");
		return json_encode($array);
	}

	protected function respondWithDocument($document, $statusCode = 200, $headers = [])
	{
		$headers['Content-Type'] = 'application/vnd.api+json';

		$this->event(new WillRespondWithDocument($document, $statusCode, $headers));

		return $this->respondWithArray($document->toArray(), $statusCode, $headers);
	}

	protected function respondWithErrors($errors, $httpCode = 500)
	{
		// return Response::json(['errors' => $errors], $httpCode);
	 	return json_encode(array('errors' => $errors));
	}

	protected function respondWithError($error, $httpCode = 500, $detail = null)
	{
		$error = ['code' => $error];

		if ($detail) {
			$error['detail'] = $detail;
	    	}

		return $this->respondWithErrors([$error], $httpCode);
		
	}

	protected function respondJson($data)
	{
		header("Content-type: application/json");
		echo json_encode($data);exit;
	}

}