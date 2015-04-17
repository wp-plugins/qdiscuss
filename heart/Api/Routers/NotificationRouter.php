<?php namespace Qdiscuss\Api\Routers;

use Qdiscuss\Core\Repositories\EloquentNotificationRepository as NotificationRepository;
use Qdiscuss\Api\Actions\Notifications\IndexAction;
use Qdiscuss\Api\Actions\Notifications\UpdateAction;

class NotificationRouter {

	public function __construct()
	{
		# code...
	}

	public static function router()
	{
		return new NotificationRouter;
	}

	/**
	 * Register the discussion-related routes
	 *
	 * @param array $routes Existing routes
	 * @return array Modified routes
	 */
	public function register_routes($routes)
	{
		$routes['/notifications'] = array(
			array( array(new IndexAction(new NotificationRepository), 'run'), \WP_JSON_Server::READABLE),
		);
		$routes['/notifications/(?P<id>\d+)'] = array(
			array( array(new UpdateAction, 'run'), \WP_JSON_Server::EDITABLE),
		);


		return $routes;
	}	
}