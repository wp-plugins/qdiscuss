<?php  namespace Qdiscuss\Core\Support;

use Qdiscuss\Core\Models\User;
use Qdiscuss\Core\Models\Guest;
use Qdiscuss\Core\Models\AccessToken;
use Illuminate\Database\Capsule\Manager as DB; 

trait Helper
{

	/**
	 * Generate the random string
	 * @param  integer $len 
	 * @return   string
	 */
	public static function rand_str($len = 8)
	{ 
		$chars="ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz-=/?~!@#$%^&*()"; // characters to build the password from 
		$string=""; 
		for (;$len >= 1;$len--) {
			$position=rand()%strlen($chars);
			$string.=substr($chars,$position,1); 
		}
		
		return $string; 
	}

	/**
	 * Create cookie when user login in or register by the WP portal
	 *
	 * @param  Qdiscuss\Core\Model\User $user
	 * @return   Qdiscuss\Core\Model\AccessToken $access_token
	 */
	public static function create_cookie($user)
	{
		$access_token = AccessToken::generate($user->id);
		$access_token->save();
		setcookie("qdiscuss_remember",  $access_token->id, time() + 60*60*24*365*5, '/');
		return $access_token;
	}

	/**
	 *  Recompile the css and js file
	 *  
	 * @return  void
	 */
	public static function recompile()
	{
		// need add generate proccess
		if (file_exists(base_path() . 'public/web/forum')) {
			$version = file_get_contents(base_path() . 'public/web/forum');
			@unlink(base_path() . 'public/web/forum-' . $version . '.js');
			@unlink(base_path() . 'public/web/forum-' . $version . '.css');
		}
	}

	public static function post_data()
	{
		return json_decode(file_get_contents("php://input"), true);
	}

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

	public static function validate_color($color)
	{
		$v = "/#([a-fA-F0-9]{3}){1,2}\b/";
		return (bool) preg_match($v, $color); 
	}

	public static function validate_number($num)
	{
		$v = "/^\d+$/";
		return (bool) preg_match($v, $num); 
	}

	public static function validate_word($word)
	{
		$v = "/^[A-Za-z]+$/";
		return (bool) preg_match($v, $word); 
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
		}else{
			return new Guest;
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
 	 *  Register user
 	 *  
 	 * @param  [object]    $user  wp_user
 	 * @return   [object]    Core\Models\User
 	 */
	public static function register_user($user)
	{
		$role = self::get_user_role($user->ID);
		$member  =User::register($user->user_login, $user->display_name, $user->user_email, '', $user->ID);
		$member->save();
		$member->activate();
		switch ($role) {
			case 'administrator':
				$member->groups()->sync([1]);
				break;
			case 'editor':
				$member->groups()->sync([3]);
				break;
			case 'author':
				$member->groups()->sync([3]);
				break;
			case 'contributor':
				$member->groups()->sync([3]);
				break;
			case 'subscriber':
				$member->groups()->sync([3]);
				break;
			default:
				$member->groups()->sync([3]);
				break;
		}

		$member->save();

		return $member;
	}

	/**
	 * Check the extension and the require qdiscussion's version
	 *
	 * @param    arrray  $require_versions
	 * @return    boolean
	 */
	public static function check_require($require_versions)
	{
		if(count($require_versions) == 1){
			return QDISCUSS_VERSION >= $require_versions['min'];
		} elseif(count($require_versions) == 2) {
			return  	QDISCUSS_VERSION >= $require_versions['min'] && QDISCUSS_VERSION <= $require_versions['max'];
		} else{
			throw new Exception("Error extension require format", 1);
		}
	}

	/**
	 * Check the table exists or not
	 *
	 * @param   $table_name table's name
	 * @return   boolean 
	 */
	public static function table_exists($table_name)
	{
		global $wpdb;
		$prefix = DB::getTablePrefix();
		$table_name = $prefix . $table_name;
		return $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
	}

	public static function runSql($sql_path)
	{
		global $wpdb, $qdiscuss_config;
		$prefix = $wpdb->prefix . $qdiscuss_config['database']['qd_prefix'];
		 	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = file_get_contents($sql_path);
	 	$new_sql = preg_replace("/_qdiscuss_prefix_/", $prefix, $sql);
	 	dbDelta($new_sql);
	}

	public static function check_rule_exsist($rule)
	{
		if (file_exists(ABSPATH . '/.htaccess')) {
			$a = extract_from_markers(ABSPATH . '/.htaccess', 'WordPress');
		
			return in_array($rule, $a);
		} else {
			return false;
		}
		
	}

	public static function write_rule_to($rule)
	{
		if (file_exists(ABSPATH . '/.htaccess')) {
			$old_rules = extract_from_markers(ABSPATH . '/.htaccess', 'WordPress');
			if (self::check_rule_exsist($rule)) {
				return true;
			} else {
				array_push($old_rules, $rule);
				return insert_with_markers(ABSPATH . '/.htaccess', 'WordPress', $old_rules);
			}
		} else {
			return false;
		}
	}

	public static function delete_rule($rule)
	{
		if (file_exists(ABSPATH . '/.htaccess')) {
			$old_rules = extract_from_markers(ABSPATH . '/.htaccess', 'WordPress');
			if (self::check_rule_exsist($rule)) {
				$new_rules = array_diff($old_rules, [$rule]);
				return insert_with_markers(ABSPATH . '/.htaccess', 'WordPress', $new_rules);
			}
		} else {
			return false;
		}
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