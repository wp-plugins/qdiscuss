<?php namespace Qdiscuss\Forum\Actions;

use Qdiscuss\Forum\Actions\BaseAction;
use Qdiscuss\Core\Support\Actor;
use Qdiscuss\Forum\Events\UserLoggedOut;
use Qdiscuss\Core\Support\Helper;

class LogoutAction
{

	public function __construct(Actor $actor)
	{
		$this->actor = $actor;
	}

	public function get()
	{
		global $qdiscuss_endpoint;

		$user = $this->actor->getUser();

		if (Helper::is_logined()) {
			$user->accessTokens()->delete();
			event(new UserLoggedOut($user));
		}

		unset($_COOKIE['qdiscuss_remember']);
		setcookie('qdiscuss_remember', null, -1, '/');
		wp_logout();
		
		header("Location: " . get_site_url() . "/" . $qdiscuss_endpoint);
		exit;
	}
}