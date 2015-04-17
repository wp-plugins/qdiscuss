<?php namespace Qdiscuss\Forum\Actions;

use Qdiscuss\Forum\Events\UserLoggedIn;
use Qdiscuss\Core\Repositories\EloquentUserRepository as UserRepositoryInterface;
use Qdiscuss\Forum\Actions\WebAction;
use Qdiscuss\Core\Models\User;
use Illuminate\Database\Capsule\Manager as DB; 
use Qdiscuss\Core\Models\AccessToken;

class LoginAction extends WebAction
{

    protected $users;

    public function __construct(UserRepositoryInterface $users)
    {
        $this->users = $users;
    }

    public function handle()
    {
        global $wpdb;

       $username = $wpdb->escape($_REQUEST['identification']);
       $password = $wpdb->escape($_REQUEST['password']);

       $login_data = array();
       $login_data['user_login'] = $username;
       $login_data['user_password'] = $password;

        $user_verify = wp_signon($login_data, false ); 

        if ( is_wp_error($user_verify) ) {
	       header("HTTP/1.1 401 Unauthorized");
              echo json_encode(array('errors' => array(array('code' => 'invalidCredentials'))));exit();
        } else {
              $wp_user = DB::select('select * from ' . $wpdb->prefix . 'users' . ' where `ID` = ?', array($user_verify->ID)); 
              $user = User::where('wp_user_id', $wp_user[0]['ID'])->first();
              
        	$access_token = AccessToken::generate($user->id);
              $access_token->save();
        	setcookie("qdiscuss_remember",  $access_token->id, time() + 60*60*24*365*5, '/');
        	echo json_encode(array('userId' => $access_token->user_id, 'token' => $access_token->id));exit();
      }
}
}