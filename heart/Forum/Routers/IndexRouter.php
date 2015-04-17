<?php namespace Qdiscuss\Forum\Routers;

use Qdiscuss\Forum\Actions\IndexAction;

class IndexRouter {

	public function __construct()
	{
		# code...
	}

	public static function router()
	{
		return new IndexRouter;
	}

	/**
         	* Register endpoits
         	*/
	public function register_routes($routes) 
	{

		$routes['/'] = array(
		 	array(array(new IndexAction(new \Qdiscuss\Core\Repositories\EloquentUserRepository), 'run'), \WP_JSON_Server::READABLE),
		);

	              return $routes;
	}

}