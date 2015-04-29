<?php  namespace Qdiscuss\Core\Support;

use Qdiscuss\Core\Models\User;

trait Helper
{
	public  static function validate_email($email)
	{
		$v = "/[a-zA-Z0-9_\-.+]+@[a-zA-Z0-9\-]+.[a-zA-Z]+/";

		return (bool)preg_match($v, $email);
	}

	public  static function validate_endpoint($endpoint)
	{
		$v = "/^[a-zA-Z\-]+[a-zA-Z]+$/";
		return (bool)preg_match($v, $endpoint) && (strlen($endpoint) <= 10);

	}

	public static function current_user()
	{
		if($user = self::is_logined()){
			$user = explode('|', $user);
			$user_name = $user[0];
			return get_user_by('login', $user_name);
		}
	}

	public static function current_forum_user()
	{
		if($user = self::is_logined()){
			$user = explode('|', $user);
			$user_name = $user[0];
			return User::where('username', $user_name)->first();
		}
	}

	/**
	  * Check whether the user is logined or not
	  *
	  * @return boolean
	  */
	public static function is_logined()
	{
	    if (count($_COOKIE)) {
	        foreach ($_COOKIE as $key => $val) {
	                    if (substr($key, 0, 19) === "wordpress_logged_in") {
	                        return $val;
	                    }
	        }
	    }

	    return false;

	}

	/**
	 *  Get wp user's role
	 *  
	 * @param  int       $uid user's id
	 * @return  string  name of the wp role
	 */
	public static function get_user_role($uid)
	{
		global $wpdb;

		$role = $wpdb->get_var("SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'wp_capabilities' AND user_id = {$uid}");
		
		if(!$role) return 'non-user';
		
		$rarr = unserialize($role);
		$roles = is_array($rarr) ? array_keys($rarr) : array('non-user');
		
		return $roles[0];
	}

	/**
	 * Get either a Gravatar URL or complete image tag for a specified email address.
	 *
	 * @param string $email The email address
	 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
	 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
	 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
	 * @param boole $img True to return a complete IMG tag False for just the URL
	 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
	 * @return String containing either just a URL or a complete image tag
	 * @source http://gravatar.com/site/implement/images/php/
	 */
	public static function get_gravatar($email, $s = 40, $d = 'mm', $r = 'g', $img = false, $atts = array() ) 
	{
	
		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $email ) ) );
		$url .= "?s=$s&d=$d&r=$r";

		if ( $img ) {
			$url = '<img src="' . $url . '"';
			foreach ( $atts as $key => $val )
				$url .= ' ' . $key . '="' . $val . '"';
				$url .= ' />';
	 	}
	    
	    	return $url;
	
	}

	/**
	 * Checks to see if the specified email address has a Gravatar image.
	 *
	 * @param         $email_address  The email of the address of the user to check
	 * @return          Whether or not the user has a gravatar
	 */
	public static function has_gravatar($email) 
	{
		$hash = md5(strtolower(trim($email)));
		$uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';

		$headers = @get_headers($uri);

		if (!preg_match("|200|", $headers[0])) {
			$has_valid_avatar = FALSE;
		} else {
			$has_valid_avatar = TRUE;
		}

		return $has_valid_avatar;
		
	}

}