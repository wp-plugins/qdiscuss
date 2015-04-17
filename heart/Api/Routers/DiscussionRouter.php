<?php namespace Qdiscuss\Api\Routers;

use Qdiscuss\Api\Actions\Discussions\IndexAction;
use Qdiscuss\Api\Actions\Discussions\ShowAction;
use Qdiscuss\Api\Actions\Discussions\UpdateAction;
use Qdiscuss\Api\Actions\Discussions\CreateAction;
use Qdiscuss\Api\Actions\Discussions\DeleteAction;

class DiscussionRouter {

	public function __construct()
	{
		# code...
	}

	public static function router()
	{
		return new DiscussionRouter;
	}

	/**
	 * Register the discussion-related routes
	 *
	 * @param array $routes Existing routes
	 * @return array Modified routes
	 */
	public function register_routes($routes)
	{
		global $qdiscuss_app, $qdiscuss_params, $qdiscuss_actor;

		$routes['/discussions'] = array(
			array(array(new IndexAction($qdiscuss_params, $qdiscuss_actor, new \Qdiscuss\Core\Search\Discussions\DiscussionSearcher(new \Qdiscuss\Core\Search\GambitManager($qdiscuss_app), new \Qdiscuss\Core\Repositories\EloquentDiscussionRepository,  new \Qdiscuss\Core\Repositories\EloquentPostRepository)), 'run'), \WP_JSON_Server::READABLE),
			array(array(new CreateAction, 'run'), \WP_JSON_Server::CREATABLE | \WP_JSON_Server::ACCEPT_JSON ),
		);
		$routes['/discussions/(?P<id>\d+)'] = array(
			array( array(new ShowAction($qdiscuss_params, $qdiscuss_actor, new \Qdiscuss\Core\Repositories\EloquentDiscussionRepository, new \Qdiscuss\Core\Repositories\EloquentPostRepository), 'run'), \WP_JSON_Server::READABLE ),
			array( array(new UpdateAction, 'run'), \WP_JSON_Server::EDITABLE),
			array( array(new DeleteAction, 'run'), \WP_JSON_Server::DELETABLE),
		);

		return $routes;
	}	
}