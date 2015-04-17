<?php namespace Qdiscuss\Api\Routers;

use Qdiscuss\Api\Actions\Posts\IndexAction;
use Qdiscuss\Api\Actions\Posts\CreateAction;
use Qdiscuss\Api\Actions\Posts\ShowAction;
use Qdiscuss\Api\Actions\Posts\UpdateAction;
use Qdiscuss\Api\Actions\Posts\DeleteAction;
use Qdiscuss\Core\Repositories\EloquentPostRepository;

class PostRouter {

	public function __construct()
	{
		# code...
	}

	public static function router()
	{
		return new PostRouter;
	}

	/**
	 * Register the discussion-related routes
	 *
	 * @param array $routes Existing routes
	 * @return array Modified routes
	 */
	public function register_routes($routes)
	{
		global $qdiscuss_actor, $qdiscuss_params;
		
		$routes['/posts'] = array(
			array( array(new IndexAction($qdiscuss_params, $qdiscuss_actor, new \Qdiscuss\Core\Repositories\EloquentPostRepository), 'run'), \WP_JSON_Server::READABLE),
			array( array(new CreateAction, 'run'), \WP_JSON_Server::CREATABLE | \WP_JSON_Server::ACCEPT_JSON),
		);

		$routes['/posts/(?P<id>\d+)'] = array(
			array( array(new ShowAction(new EloquentPostRepository), 'run'), \WP_JSON_Server::READABLE),
			array( array(new UpdateAction, 'run'), \WP_JSON_Server::EDITABLE),
			array( array(new DeleteAction, 'run'), \WP_JSON_Server::DELETABLE),
		);

		return $routes;
	}	
}