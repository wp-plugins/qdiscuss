<?php namespace Qdiscuss\Dashboard;

use Qdiscuss\Core\Models\User;
use Qdiscuss\Core\Models\Group;
use Qdiscuss\Core\Models\Setting;
use Qdiscuss\Core\Models\Permission;
use Qdiscuss\Core\Models\Discussion;
use Qdiscuss\Core\Models\CommentPost;
use Illuminate\Database\Capsule\Manager as DB; 
use Qdiscuss\Core\Support\Helper;

class Bridge {

	use Helper;

	public static $table_names = array(
 		"groups",
 		"permissions",
 		"users_groups",
 		"access_tokens",
 		"users",
 		"users_discussions",
 		"discussions",
 		"posts",
 		"notifications",
 		"activity",
 		"config",
 		"attachments",
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
		self::seed_groups();
	 	self::seed_permissions();
		add_option( 'qdiscuss_db_version', QDISCUSS_VERSION );
		add_option( 'qdiscuss_version', QDISCUSS_VERSION );
		self::create_qd_dir();
		self::write_rule_to('SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1');
	}

	/**
	 * Deactivate the plugin
	 *
	 * @return  void
	 */     
	public static function deactivate()
	{
                    	// nothing to
	}

	/**
	 *  Uninstall the plugin hook, just remove all the data tables of QDiscuss
	 *  
	 * @return void
	 */
	public static function uninstall()
	{
		self::drop_tables();
		self::delete_dirs();
		delete_option( 'qdiscuss_db_version' );
		delete_option( 'qdiscuss_version' );
		self::delete_rule('SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1');
	}

	/**
	 * Create QDiscuss's data structure when QDiscuss has been activated
	 * 
	 * @return void
	 */
	public static function create_tables()
	{
	 	global $wpdb;
		$prefix = DB::getTablePrefix();
	 	
	 	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	 	foreach (self::$table_names as $table) {
	 		$table_name = $prefix . $table;

	 		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	 			$sql = file_get_contents(__DIR__ . "/migrations/" . $table . "-schema.sql");
	 			$new_sql = preg_replace("/_qdiscuss_prefix_/", $prefix, $sql);
	 			dbDelta($new_sql);

	 			if($table == 'users'){
	 				$user = self::register_user(self::current_user());
	 				self::create_cookie($user);
	 			}

	 			if($table == 'config'){
	 				self::seed_config();
	 			}

	 			if($table == 'posts'){
	 				self::seed_discussions();
	 			}
	 		}
	 	}
	 	
		// be sure the current user has migrated into qdiscuss
		if(!User::where('username', self::current_user()->user_login)->first()){
			$user = self::register_user(self::current_user());
			self::create_cookie($user);
		}
		
	}

	/**
	 * Drop QDiscuss's data structure when QDiscuss has been deleted
	 * 
	 * @return void
	 */
	public static function drop_tables()
	{
	 	global $wpdb;

		$prefix = DB::getTablePrefix();
	 	// $tables = self::$table_names;
	 	// another method tp drop table, but some danger here, so not use it below
	 	$like_name = '%' .  $prefix .'%';
	 	$qd_table_names = array_pluck($wpdb->get_results("SHOW TABLES LIKE '" . $like_name . "'", ARRAY_A), 'Tables_in_' . DB_NAME .' (' .  $like_name . ')');
	 	
	 	foreach ($qd_table_names as $table) {
	 		$sql = "drop table `" . $table . "`;" ;
	 		DB::statement($sql);
	 	}
	 	
	}

	/**
	 *  Delete Files which QDiscuss created
	 */
	public static function delete_dirs()
	{
		app('files')->deleteDirectory(qd_base_wp_path());
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
		Group::unguard();
		Group::truncate();
		
		$groups = [
		            ['Admin', 'Admins', '#B72A2A', 'wrench'],
		            ['Guest', 'Guests', null, null],
		            ['Member', 'Members', null, null],
		            ['Mod', 'Mods', '#80349E', 'bolt']
		];
		
		foreach ($groups as $group) {
			Group::create([
		              	'name_singular' => $group[0],
		                	'name_plural' => $group[1],
		              	'color' => $group[2],
		              	'icon' => $group[3]
		            	]);
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
		
		$permissions = [
		           // Guests can view the forum
		           [2, 'forum.view'],
		           // Members can create and reply to discussions + edit their own stuff
		           [3, 'forum.startDiscussion'],
		           [3, 'discussion.reply'],
		           // Moderators can edit + delete stuff and suspend users
		           [4, 'discussion.delete'],
		           [4, 'discussion.rename'],
		           [4, 'post.delete'],
		           [4, 'post.edit'],
		           [4, 'user.suspend'],
		];

		foreach ($permissions as &$permission) {
			$permission = array(
				'group_id'    => $permission[0],
				'permission' => $permission[1],
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
			array("key" => "extensions_enabled", "value" => '[]'),
			array("key" => "forum_language", "value" => 'en'),
		);
	
		Setting::insert($settings);

	}

	/**
	 *  Seed the first discussion data 
	 *  
	 * @return void
	 */
	public static function seed_discussions()
	{
		Discussion::unguard();
		CommentPost::unguard();
		Discussion::truncate();
		CommentPost::truncate();

		$current_user = self::current_forum_user();

		$discussion = Discussion::create([
		    'title'         => 'Welcome To QDiscuss World',
		    'start_time'    => date("Y-m-d H:i:s"),
		    'start_user_id' => $current_user->id,
		]);

		$post = CommentPost::create([
             		'discussion_id' => $discussion->id,
                		'number'        => 1,
                		'time'          => $discussion->start_time,
                		'user_id'       => $discussion->start_user_id,
                		'content'       => "Created by http://colorvila.com"
            		]);

		$discussion->start_post_id = $post->id;
	             $discussion->last_time        = $post->time;
            		$discussion->last_user_id     = $post->user_id;
            		$discussion->last_post_id     = $post->id;
            		$discussion->last_post_number = $post->number;
            		$discussion->number_index     = $post->number;
            		$discussion->comments_count = 1;

            		$discussion->save();

	}

	public static function create_qd_dir()
	{
		if(!file_exists(qd_extensions_path())){
			mkdir(qd_extensions_path(), 0777, true);
		}

		if(!file_exists(qd_storage_path())){
			mkdir(qd_storage_path(), 0777, true);
		}
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

		// @todo add a function to migrate the display_name from wordpress, in future can be deleted
		if ($user = User::where($qd_field, $username)->first()) {
			if (!$user->display_name) {
				$wp_user = get_user_by($wp_field, $username);
				$user->display_name = $wp_user->display_name;
				$user->save();
			}

			self::create_cookie($user);
		} else {
			$user = get_user_by($wp_field, $username);
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
			$user->display_name = $_POST['display_name'];
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


