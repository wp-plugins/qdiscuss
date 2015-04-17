<?php namespace Qdiscuss\Admin\Routers;

use Qdiscuss\Admin\Actions\IndexAction;
use Qdiscuss\Core\Repositories\EloquentUserRepository as UserRepository;

class AdminRouter {

	public function __construct()
	{
		# code...
	}

	public static function router()
	{
		return new AdminRouter;
	}

	/**
	 * Register the discussion-related routes
	 *
	 * @param array $routes Existing routes
	 * @return array Modified routes
	 */
	public function register_routes($routes)
	{
		$routes['/admin'] = array(
			array( array(new IndexAction(new UserRepository), 'run'), \WP_JSON_Server::READABLE),
		);

		return $routes;
	}	
}