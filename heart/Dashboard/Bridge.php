<?php namespace Qdiscuss\Dashboard;

use Qdiscuss\Core\Models\User;
use Qdiscuss\Core\Models\Group;
use Qdiscuss\Core\Models\Setting;
use Qdiscuss\Core\Models\Permission;
use Illuminate\Database\Capsule\Manager as DB; 
use Qdiscuss\Core\Support\Helper;

class Bridge {

	use Helper;

	public static $table_names = array(
 		"groups",
 		"permissions",
 		"users_groups",
 		"users",
 		"access_tokens",
 		"users_discussions",
 		"discussions",
 		"posts",
 		"notifications",
 		"activity",
 		"config",
 		"migrations",
 	);

	public static function register_rewrite()
	{
		$that = new static;
		add_rewrite_rule( '^' . $that->get_url_prefix() . '/?$','index.php?json_route=/','top' );
		add_rewrite_rule( '^' . $that->get_url_prefix() . '(.*)?','index.php?json_route=$matches[1]','top' );
	}

	protected function get_url_prefix()
	{
		global $qdiscuss_endpoint;
		$qdiscuss_endpoint = Setting::getEndPoint();
		return apply_filters( 'json_url_prefix', $qdiscuss_endpoint);
	}

	/**
	 * Activate the plugin, Just do the database setup and copy the wp'users table datas into QDiscuss's users table
	 *
	 * @return  void
	 */
	public static function activate()
	{
		self::create_tables();
	} 

	/**
	 * Deactivate the plugin
	 *
	 * @return  void
	 */     
	public static function deactivate()
	{
                    	// nothing to do
	}

	/**
	 *  Uninstall the plugin hook, just remove all the data tables of QDiscuss
	 *  
	 * @return void
	 */
	public static function uninstall()
	{
		self::drop_tables();
	}

	/**
	 * Create QDiscuss's data structure when QDiscuss has been activated
	 * 
	 * @return void
	 */
	public static function create_tables()
	{
	 	global $wpdb, $qdiscuss_config;
		$prefix = $wpdb->prefix . $qdiscuss_config['database']['qd_prefix'];
	 	
	 	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	 	foreach (self::$table_names as $table) {
	 		$table_name = $prefix . $table;

	 		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	 			$sql = file_get_contents(__DIR__ . "/migrations/" . $table . "-schema.sql");
	 			$new_sql = preg_replace("/_qdiscuss_prefix_/", $prefix, $sql);
	 			dbDelta($new_sql);

	 			if($table == 'users'){
	 				self::register_user(self::current_user());
	 			}

	 			if($table == 'config'){
	 				self::seed_config();
	 			}
	 		}
	 	}
	 	
	 	self::seed_groups();
	 	self::seed_permissions();

		// be sure the current user has migrated into qdiscuss
		if(!User::where('username', self::current_user()->user_login)->first()){
			self::register_user(self::current_user());
		}
		
	}

	/**
	 * Drop QDiscuss's data structure when QDiscuss has been deleted
	 * 
	 * @return void
	 */
	public static function drop_tables()
	{
	 	global $wpdb, $qdiscuss_config;

		$prefix = $qdiscuss_config['database']['prefix'];
	 	$tables = self::$table_names;
	 	
	 	foreach ($tables as $table) {
	 		$sql = "drop table `" . $prefix . $table . "`;" ;
	 		DB::statement($sql);	
	 	}
	}

	/**
	 * Migrate wp users datas to qdiscuss user's table
	 * 
	 * @return void
	 */
	public static function migrate_users()
	{
	 	global $wpdb;

	 	$users = $wpdb->get_results('select * from ' . $wpdb->prefix . 'users');
	       	
	       	User::truncate();
	       	
	       	foreach ($users as $key => $user) {
	       		self::register_user($user);
	              }
	}

	/**
	 * Seed the groups data 
	 * 
	 * @return void
	 */
	public static function seed_groups()
	{
		Group::truncate();
		$groups = array('Administrator', 'Guest', 'Member', 'Moderator', 'Staff');
		        foreach ($groups as $group) {
		            Group::create(array('name' => $group));
		        }
	}

	/**
	 *  Seed the permissions data 
	 *  
	 * @return void
	 */
	public static function seed_permissions()
	{
		Permission::truncate();
		
		$permissions = array(
		            // Guests can view the forum
		            array('group.2' , 'forum'          , 'view'),
		            array('group.2' , 'forum'          , 'register'),
		            // Members can create and reply to discussions + edit their own stuff
		            array('group.3' , 'forum'          , 'startDiscussion'),
		            array('group.3' , 'discussion'     , 'editOwn'),
		            array('group.3' , 'discussion'     , 'reply'),
		            array('group.3' , 'post'           , 'editOwn'),
		            // Moderators can edit + delete stuff and suspend users
		            array('group.4' , 'discussion'     , 'delete'),
		            array('group.4' , 'discussion'     , 'edit'),
		            array('group.4' , 'post'           , 'delete'),
		            array('group.4' , 'post'           , 'edit'),
		            array('group.4' , 'user'           , 'suspend'),
		);

		foreach ($permissions as &$permission) {
			$permission = array(
				'grantee'    => $permission[0],
				'entity'     => $permission[1],
				'permission' => $permission[2],
			);
		}

		Permission::insert($permissions);
	}

	/**
	 *  Seed the config data 
	 *  
	 * @return void
	 */
	public function seed_config()
	{
		Setting::truncate();

		$settings = array(
			array("key" => "forum_title", "value" => "QDiscuss"),
			array("key" => "forum_welcome_title", "value" => "Welcome to QDiscuss"),
			array("key" => "forum_description", "value" => "An Amazing Forum Plugin Base On WordPress By <a href='http://colorvila.com'>ColorVila</a>"),
			array("key" => "forum_endpoint", "value" => "qdiscuss"),
		);
	
		Setting::insert($settings);

	}

	public static function register_user($user)
	{
		$role = self::get_user_role($user->ID);
		$member  =User::register($user->user_login, $user->user_email, '', $user->ID);
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
	}

	/**
	 *  Hook to wp user login, if user has not signed up to Qdiscuss, then signup
	 *
	 * @param  string username
	 * @return  void
	 */
	public  static function hook_user_login($username)
	{
		if (self::validate_email($username)){
			$wp_field = $qd_field = 'email';
		} else{
			$wp_field = 'login';
			$qd_field = 'username';
		}

		if(!User::where($qd_field, $username)->first()){
			$user = get_user_by('login', $username);
			self::register_user($user);
		}
		
	}


	/**
	 *  Hook to wp user register
	 *
	 * @param  [int] [registered user's id]
	 * @return   void
	 */
	public  static function hook_user_register($uid)
	{
		$user = get_user_by('id', $uid);

		if($user) self::register_user($user);
	}

	/**
	 *  Hook to wp user's profile update in admin
	 *
	 * @param  int $uid
	 * @return 
	 */
	public  static function hook_profile_update($uid)
	{
		$user = User::where('wp_user_id', $uid)->first();
		
		if($user){
			$user->email = $_POST['email'];
			$user->save();
		}
	}

	/**
	 *  Hook to wp user's profile update in user's page
	 *
	 * @param  int $uid
	 * @return 
	 */
	public static function hook_user_profile_update($uid)
	{
		
	}

	/**
	 * Hook to wp user delete event
	 * 
	 * @param   int $uid 
	 * @return   void
	 */
	public  static function hook_user_delete($uid)
	{
		//$user = User::where('wp_user_id', $uid)->first();
		
		//just for thinking the reated data's change
	}
}


