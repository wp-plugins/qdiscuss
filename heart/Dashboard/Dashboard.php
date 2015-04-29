<?php namespace Qdiscuss\Dashboard;

use Qdiscuss\Core\Models\User;
use Qdiscuss\Core\Models\Setting;
use Qdiscuss\Core\Models\Group;
use Qdiscuss\Core\Support\Helper;
/*
 |--------------------------------------------------------------------------
 | Dashboard Setting Page Handler For WordPress
 |--------------------------------------------------------------------------
 |
 */
class Dashboard  {

	use Helper;	
	
	public static function qdiscuss_enqueue_admin()
	{		
		// General Admin Script
		wp_enqueue_script(
			'qdiscuss-admin',
			plugins_url( 'public/dashboard/js/qdiscuss-admin.js', __DIR__.'/../../../'),
			array(),
			QDISCUSS_VERSION
		);

		$translation_array = array(
			'config_settings_redirect' =>admin_url('admin.php?page=qdiscuss-settings'),			
		);
		wp_localize_script( 'qdiscuss-admin', 'params', $translation_array );

		// Admin Style
		// wp_register_style(
		// 	'qdiscuss-admin',
		// 	plugins_url( 'public/dashboard/css/qdiscuss-admin.css', __DIR__.'/../../../'),
		// 	false,
		// 	QDISCUSS_VERSION,
		// 	'all'
		// );
				
	}

	public static function qdiscuss_admin_menu()
	{
		global  $wp_version;

		$pages = array();
		
		$menu_icon = 'dashicons-star-filled';
		if ( version_compare( $wp_version, '3.8', '<' ) )
			$menu_icon = '';

		$title = 'QDiscuss';
		$menu_slug = 'qdiscuss-settings';
		$pages[] = add_menu_page(
			$title,
			$title,
			'add_users',
			$menu_slug,
			array('\Qdiscuss\Dashboard\Dashboard', 'qdiscuss_settings_page'),
			$menu_icon
		);

		$pages[] = add_submenu_page(
			$menu_slug, 
			'Users', 
			'Users', 
			'add_users', 
			'qdiscuss-users',
			array('\Qdiscuss\Dashboard\Dashboard', 'qdiscuss_users_page')
		);		

		$pages[] = add_submenu_page(
			'admin.php', 
			'role setting', 
			'role setting', 
			'manage_options', 
			'qdiscuss-config-settings',
			array('\Qdiscuss\Dashboard\Dashboard', 'qdiscuss_config_settings_page')
		);

		$pages[] = add_submenu_page(
			'admin.php', 
			'role setting', 
			'role setting', 
			'manage_options', 
			'qdiscuss-roles-settings',
			array('\Qdiscuss\Dashboard\Dashboard', 'qdiscuss_roles_settings_page')
		);


		$pages = apply_filters( 'qdiscuss_admin_pages', $pages);

		// foreach ( $pages as $page ) {
		//         add_action( 'admin_print_styles-' . $page, array('\Qdiscuss\Dashboard\Dashboard', 'qdiscuss_admin_page_styles' ));
		// }

	}

	public static function qdiscuss_ajax_config_settings_save() {
		wp_parse_str(stripslashes($_POST['data']), $data);
		foreach ($data as $key => $value) {			

			if($key == 'forum_endpoint') {
				if(self::validate_endpoint(trim($value))){		
					Setting::setValue($key, $value);				
				}else{
					die('2'); 
				}
			} else {
				Setting::setValue($key, $value);
			}
		}		
		
		die('1');
	}

	public static function qdiscuss_admin_page_styles()
	{
		wp_enqueue_style( 'qdiscuss-admin' );
	}

	public static function qdiscuss_admin_init()
	{			
		add_action( 'admin_notices',  '\Qdiscuss\Dashboard\Dashboard::qdiscuss_notices' );		
	}

	public static function qdiscuss_notices() {
		
		if ( get_option('permalink_structure') == '' ) { 
			
			$message = __( 'One more step to make QDiscuss work, you need <a href="'  .  admin_url('options-permalink.php') . '">enable Pretty Permalinks</a>, see more details about <a href="https://codex.wordpress.org/Using_Permalinks">Pretty Permalink</a>', 'qdiscuss' );
			?>

			 <div class="error">
			        <p><?php echo $message; ?></p>
			 </div>
			 <?php
		}

	}


	public static function qdiscuss_config_settings_page()
	{

		$key = $_GET['key'];

		$value = Setting::getValueByKey($key);

		include __DIR__ . "/views/html-config-settings.php";
	}

	
	public static function qdiscuss_settings_page()
	{
		$settings = Setting::all();
		$endpoint = Setting::getEndPoint();
		
		include __DIR__ . "/views/html-settings-page.php";
	}

	public static function qdiscuss_users_page()
	{
		global $wpdb;
		$search_name = $_POST['search_name'];
		if($search_name)
			$wpdb->query("select * from " . $wpdb->prefix . "users where user_login like '%" . $search_name . "%'");
		else
			$wpdb->query("select * from " . $wpdb->prefix . "users");

		$total = $wpdb->num_rows; 
		$per_page = 20;
		$page = isset( $_GET['pagination'] ) ? abs( (int) $_GET['pagination'] ) : 1;
		$pagination = paginate_links( array(
						'base' => add_query_arg( 'pagination', '%#%' ),
						'format' => '',
						'prev_text' => __('&laquo;'),
						'next_text' => __('&raquo;'),
						'total' => ceil($total / $per_page),
						'current' => $page
					));
		$start_page = ($page-1) * $per_page;

		if($search_name)
			$wp_users = $wpdb->get_results("select ID, user_login from " . $wpdb->prefix . "users  where user_login like '%" . $search_name . "%' LIMIT " . $start_page . "," . $per_page , ARRAY_A ); 
		else 
			$wp_users = $wpdb->get_results("select ID, user_login from " . $wpdb->prefix . "users LIMIT " . $start_page . "," . $per_page, ARRAY_A ); 
		
		foreach ($wp_users as &$wp_user) {
			if($user = User::where('wp_user_id', $wp_user["ID"])->with('groups')->first()){
				$wp_user['group'] = $user->groups[0]->name;
				$wp_user['user_id'] = $user->id;
			}else{
				$wp_user['group'] = 'None';
				$wp_user['user_id'] = 0;
			}
		}

		include __DIR__ . "/views/html-users-page.php";
	}


	public static function qdiscuss_roles_settings_page()
	{
		global $wpdb, $qdiscuss_config;

		$user_id = $_GET['id'];
		$wp_user_id = $_GET['wp_user_id'];
		$qdiscuss_roles = Group::all();

		if($_POST){
			if(!$user_id){
				$user = self::register_user(get_user_by('id', $wp_user_id));
			}else{
				$user = User::find($user_id);
			}

			if($group_id = $_POST['qdiscuss_group']){
				if($user->groups[0]->id != $group_id) {
					$user->groups()->sync([$group_id]);
				}
			}

			$user_id = $user->id;
			if($user_id){
				$user = User::find($user_id);
			}else{
				$user = get_user_by('id', $wp_user_id);
			}
			

			include __DIR__ . "/views/html-roles-settings.php";exit();

		}

		if($user_id){
			$user = User::find($user_id);
		}else{
			$user = get_user_by('id', $wp_user_id);
		}
		

		include __DIR__ . "/views/html-roles-settings.php";

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

		return $member;
	}

}