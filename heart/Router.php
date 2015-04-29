<?php namespace Qdiscuss;

require_once ABSPATH . '/wp-includes/pluggable.php';
use Qdiscuss\Core\Support\Helper;
use Qdiscuss\Core\Models\Setting;

class Router {

	use Helper;

	public static function routes()
	{
		global $qdiscuss_endpoint, $qdiscuss_actor, $wpdb;

		$config_table_name = $wpdb->prefix . 'qd_' . 'config';
		if($wpdb->get_var("SHOW TABLES LIKE '$config_table_name'") != $config_table_name) {
			$qdiscuss_endpoint = 'qdiscuss';
		} else {
			$qdiscuss_endpoint = Setting::getEndPoint();
			\Qdiscuss::init();
			$qdiscuss_actor->setUser(self::current_forum_user());
			\Qdiscuss\Api\Serializers\BaseSerializer::setActor($qdiscuss_actor);
		}
		
		return array(
			'/' . $qdiscuss_endpoint => '\Qdiscuss\Forum\Actions\IndexAction',
			'/' . $qdiscuss_endpoint . '/login' => '\Qdiscuss\Forum\Actions\LoginAction',
			'/' . $qdiscuss_endpoint . '/logout' => '\Qdiscuss\Forum\Actions\LogoutAction',
			'/' . $qdiscuss_endpoint . '/discussions' => '\Qdiscuss\Api\Actions\Discussions\IndexAction',
			'/' . $qdiscuss_endpoint . '/discussions' . '/:number' => '\Qdiscuss\Api\Actions\Discussions\ShowAction',
			'/' . $qdiscuss_endpoint . '/posts' => '\Qdiscuss\Api\Actions\Posts\IndexAction',
			'/' . $qdiscuss_endpoint . '/posts' . '/:number' => '\Qdiscuss\Api\Actions\Posts\ShowAction',
			'/' . $qdiscuss_endpoint . '/activity'  => '\Qdiscuss\Api\Actions\Activity\IndexAction',
			'/' . $qdiscuss_endpoint . '/users'  => '\Qdiscuss\Api\Actions\Users\IndexAction',
			'/' . $qdiscuss_endpoint . '/users/([a-zA-Z0-9-_]+)'  => '\Qdiscuss\Api\Actions\Users\ShowAction',
			'/' . $qdiscuss_endpoint . '/users/:number/avatar'  => '\Qdiscuss\Api\Actions\Users\UploadAvatarAction',
			'/' . $qdiscuss_endpoint . '/notifications' => 'Qdiscuss\Api\Actions\Notifications\IndexAction',
			// '/' . $qdiscuss_endpoint . '/groups' => 'Qdiscuss\Api\Actions\Groups\IndexAction',
			'/' . $qdiscuss_endpoint . '/admin' => 'Qdiscuss\Admin\Actions\IndexAction',
		);
	}
}
