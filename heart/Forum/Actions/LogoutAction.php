<?php namespace Qdiscuss\Forum\Actions;

use Qdiscuss\Forum\Actions\BaseAction;

class LogoutAction
{

	public function run()
	{
		global $qdiscuss_actor, $qdiscuss_endpoint;
		
		$user = $qdiscuss_actor->getUser();
		
		if ($user->exists) {
		    $user->accessTokens()->delete();
		}
	
		unset($_COOKIE['qdiscuss_remember']);
		setcookie('qdiscuss_remember', null, -1, '/');
		wp_logout();
		header("Location: " . get_site_url() . "/" . $qdiscuss_endpoint);
	}
}