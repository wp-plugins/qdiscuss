<?php namespace Qdiscuss\Forum\Actions\Session;

use Qdiscuss\Forum\Models\User;
use Qdiscuss\Forum\Serializers\UserSerializer;
use Qdiscuss\Forum\Actions\BaseAction;
use Qdiscuss\Core\Support\Helper;

class SignupAction extends BaseAction
{
    use Helper;

    public function __construct()
    {
    	# code...
    }

    public function handle()
    {
    	# code...
    }

    public function run()
    {
            require_once(ABSPATH . WPINC . '/registration.php');
            global $wpdb;
   
            if (self::is_logined()) {
            		header( 'Location: /qdiscuss');
            } else {
		$errors = array();
		$errors["errors"] = array();

	              if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	              		$post_data = json_decode(file_get_contents('php://input'), true)["data"];
			
	              		$username = $post_data['username'];
	               	if ( strpos($username, ' ') !== false ) { 
				array_push($errors['errors'], array("detail" => "Sorry, no spaces allowed in usernames", "path" => "username"));
	                    	}
	              		
	              		if(empty($username)) { 
	                        		array_push($errors['errors'], array("detail" => "Sorry, no usernames", "path" => "username"));
	              		} elseif( username_exists( $username ) ) {
	              			array_push($errors['errors'], array("detail" => "Username already exists, please try another", "path" => "username"));
	                    	}

	              		$email = $post_data['email'];
	              		if( !is_email( $email ) ) { 
	              			array_push($errors['errors'], array("detail" => "Please enter a valid email", "path" => "email"));
	                    	} elseif( email_exists( $email ) ) {
	                        		array_push($errors['errors'], array("detail" => "This email address is already in use", "path" => "email"));
	                    	}
	                    
	              		
	              		$password = $post_data['password'];
	              		if(0 === preg_match("/.{6,}/", $password)){
	              			array_push($errors['errors'], array("detail" => "Password must be at least six characters", "path" => "password"));
	              		}

	                    	if(0 === count($errors["errors"])) {
				$new_user_id = wp_create_user( $username, $password, $email );
				
				$user = User::where('wp_user_id', $new_user_id)->first();
				
				$serializer = new UserSerializer;
				$document = $this->document()->setData($serializer->resource($user));
				echo $this->respondWithDocument($document, 201);exit;
	                    	}else{
	                    		header("HTTP/1.1 401 Unauthorized");
	                        		echo json_encode($errors);exit;
	                   	}

	              }
	}

     }
}