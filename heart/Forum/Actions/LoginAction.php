<?php namespace Qdiscuss\Forum\Actions;

use Qdiscuss\Forum\Events\UserLoggedIn;
use Qdiscuss\Forum\Actions\WebAction;
use Qdiscuss\Core\Models\User;
use Illuminate\Database\Capsule\Manager as DB; 
use Qdiscuss\Core\Models\AccessToken;
use Qdiscuss\Core\Support\Helper;

class LoginAction extends WebAction
{
	use Helper;

	public function __construct()
	{
		
	}

	public function handle()
	{
	  # code...
	}

	public function post()
	{
		global $wpdb;

		$login_data = array();
		$login_data['user_login'] = array_get(self::post_data(), 'identification');
		$login_data['user_password'] = array_get(self::post_data(), 'password');

		$user_verify = wp_signon($login_data, false ); 

		if ( is_wp_error($user_verify) ) {
			header("HTTP/1.1 401 Unauthorized");
			echo json_encode(array('errors' => array(array('code' => 'invalidCredentials'))));exit();
		} else {
			$wp_user = DB::select('select * from ' . $wpdb->prefix . 'users' . ' where `ID` = ?', array($user_verify->ID)); 
			$user = User::where('wp_user_id', $wp_user[0]['ID'])->first();
			$access_token = self::create_cookie($user);
			return $this->respondJson(array('userId' => $access_token->user_id, 'token' => $access_token->id));
		}
	}
}
