<?php namespace Qdiscuss\Api\Routers;

use Qdiscuss\Core\Repositories\EloquentUserRepository as UserRepository;
use Qdiscuss\Core\Repositories\EloquentActivityRepository as ActivityRepository;
use Qdiscuss\Api\Actions\Activity\IndexAction;

class ActivityRouter {

	public function __construct()
	{
		# code...
	}

	public static function router()
	{
		return new ActivityRouter;
	}

	/**
	 * Register the discussion-related routes
	 *
	 * @param array $routes Existing routes
	 * @return array Modified routes
	 */
	public function register_routes($routes)
	{
		$routes['/activity'] = array(
			array( array(new IndexAction(new UserRepository, new ActivityRepository), 'run'), \WP_JSON_Server::READABLE),
			// array( array(new CreateAction, 'run'), \WP_JSON_Server::CREATABLE | \WP_JSON_Server::ACCEPT_JSON),
		);

		return $routes;
	}	
}