<?php namespace Qdiscuss\Api\Routers;

use Qdiscuss\Core\Search\Discussions\DiscussionSearcher;
use Qdiscuss\Core\Repositories\EloquentDiscussionRepository;
use Qdiscuss\Core\Repositories\EloquentPostRepository;
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
		global $qdiscuss_app;

		$routes['/discussions'] = array(
			array(array(new IndexAction(new DiscussionSearcher($qdiscuss_app['qdiscuss.discussions.gambits'], new EloquentDiscussionRepository,  new EloquentPostRepository)), 'run'), \WP_JSON_Server::READABLE),
			array(array(new CreateAction, 'run'), \WP_JSON_Server::CREATABLE | \WP_JSON_Server::ACCEPT_JSON ),
		);
		$routes['/discussions/(?P<id>\d+)'] = array(
			array( array(new ShowAction(new EloquentDiscussionRepository, new EloquentPostRepository), 'run'), \WP_JSON_Server::READABLE ),
			array( array(new UpdateAction, 'run'), \WP_JSON_Server::EDITABLE),
			array( array(new DeleteAction, 'run'), \WP_JSON_Server::DELETABLE),
		);

		return $routes;
	}

}