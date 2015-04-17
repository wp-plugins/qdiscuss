<?php namespace Qdiscuss\Forum\Routers;

use Qdiscuss\Forum\Actions\LoginAction;
use Qdiscuss\Forum\Actions\LogoutAction;

class SessionRouter {

	public function __construct()
	{
		# code...
	}

	public static function router()
	{
		return new SessionRouter;
	}

	/**
	 * Register the discussion-related routes
	 *
	 * @param array $routes Existing routes
	 * @return array Modified routes
	 */
	public function register_routes($routes)
	{

		$routes['/login'] = array(
			array( array(new LoginAction(new \Qdiscuss\Core\Repositories\EloquentUserRepository), 'handle'), \WP_JSON_Server::CREATABLE | \WP_JSON_Server::ACCEPT_JSON ),
		);

		$routes['/logout'] = array(
			array( array(new LogoutAction, 'run'), \WP_JSON_Server::READABLE),
		);

		return $routes;
	}	
}