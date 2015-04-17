<?php namespace Qdiscuss\Api\Routers;

use Qdiscuss\Api\Actions\Users\IndexAction;
use Qdiscuss\Api\Actions\Users\ShowAction;
use Qdiscuss\Api\Actions\Users\UpdateAction;
use Qdiscuss\Api\Actions\Users\DeleteAction;
use Qdiscuss\Api\Actions\Users\UploadAvatarAction;
// use Qdiscuss\Actions\Session\SignupAction;

class UserRouter {

	public function __construct()
	{
		# code...
	}

	public static function router()
	{
		return new UserRouter;
	}

	/**
	 * Register the discussion-related routes
	 *
	 * @param array $routes Existing routes
	 * @return array Modified routes
	 */
	public function register_routes($routes)
	{
		global $qdiscuss_actor, $qdiscuss_params, $qdiscuss_app;

		$routes['/users'] = array(
			array( array(new IndexAction(new \Qdiscuss\Core\Search\Users\UserSearcher(new \Qdiscuss\Core\Search\GambitManager($qdiscuss_app), new \Qdiscuss\Core\Repositories\EloquentUserRepository)), 'run'), \WP_JSON_Server::READABLE),
			// array(array(new SignupAction, 'run'), \WP_JSON_Server::CREATABLE | \WP_JSON_Server::ACCEPT_JSON ),
		);
		$routes['/users/(?P<id>\d+)'] = array(
			array( array(new ShowAction($qdiscuss_params, $qdiscuss_actor, new \Qdiscuss\Core\Repositories\EloquentUserRepository), 'run'), \WP_JSON_Server::READABLE),
			array( array(new UpdateAction, 'run'), \WP_JSON_Server::EDITABLE),
		);
		$routes['/users/(?P<id>\w+)'] = array(
			array( array(new ShowAction($qdiscuss_params, $qdiscuss_actor, new \Qdiscuss\Core\Repositories\EloquentUserRepository), 'run'), \WP_JSON_Server::READABLE),
			array( array(new DeleteAction, 'run'), \WP_JSON_Server::DELETABLE),
		);
		$routes['/users/(?P<id>\d+)/avatar'] = array(
			array( array(new UploadAvatarAction, 'run'), \WP_JSON_Server::CREATABLE),
		);

		return $routes;
	}	
}