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

		$prefix = $wpdb->prefix . $qdiscuss_config['database']['prefix'];
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

	public static function qdiscuss_register_required_plugins() 
	{

		$plugins = array(

			// This is an example of how to include a plugin pre-packaged with a theme.
			array(
				'name'               => 'Json Rest Api', // The plugin name.
				'slug'               => 'json-rest-api', // The plugin slug (typically the folder name).
				'source'             =>'https://downloads.wordpress.org/plugin/json-rest-api.1.2.1.zip', // The plugin source.
				'required'           => true, // If false, the plugin is only 'recommended' instead of required.
				'version'            => '1.2.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
				'force_activation'   => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
				'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			),

	   	);

		$config = array(
			'default_path' => '',                      // Default absolute path to pre-packaged plugins.
			'menu'         => 'qdiscuss-install-plugins', // Menu slug.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
		             'strings'      => array(
			            'page_title'                      => __( 'Install Required Plugins', 'qdiscuss' ),
			            'menu_title'                      => __( 'Install Plugins', 'qdiscuss' ),
			            'installing'                      => __( 'Installing Plugin: %s', 'qdiscuss' ), // %s = plugin name.
			            'oops'                            => __( 'Something went wrong with the plugin API.', 'qdiscuss' ),
			            'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s).
			            'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s).
			            'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s).
			            'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
			            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
			            'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s).
			            'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s).
			            'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s).
			            'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
			            'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins' ),
			            'return'                          => __( 'Return to Required Plugins Installer', 'qdiscuss' ),
			            'plugin_activated'                => __( 'Plugin activated successfully.', 'qdiscuss' ),
			            'complete'                        => __( 'All plugins installed and activated successfully. %s', 'qdiscuss' ), // %s = dashboard link.
			            'nag_type'                        => 'updated', // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
			)
		);
	    
	    	tgmpa( $plugins, $config );

	}
}


