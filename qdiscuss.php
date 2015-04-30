<?php
/*
Plugin Name: QDiscuss
Plugin URI: 
Description: An Amazing Forum WordPress Plugin
Version: 0.0.3
Author: ColorVila Team
Author URI: http://colorvila.com/plugin-qdiscuss
*/
if(PHP_VERSION < '5.4') die(' PHP_VERSION need >= 5.4, please upgrade your PHP');

const QDISCUSS_VERSION = '0.0.3';

require __DIR__ . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Load the global config and database connection
|--------------------------------------------------------------------------
|
*/
require __DIR__.'/bootstrap/load.php';

/*
|--------------------------------------------------------------------------
| Launch The QDiscuss Application
|--------------------------------------------------------------------------
|
*/
require __DIR__.'/bootstrap/init.php';


/*
|--------------------------------------------------------------------------
| Register Some Helper Functions
|--------------------------------------------------------------------------
|
*/
require __DIR__.'/bootstrap/helper.php';

require __DIR__.'/bootstrap/helper.php';

/*
|--------------------------------------------------------------------------
| Hook-ups into WordPress Core
|--------------------------------------------------------------------------
|
*/
// QDiscuss Plugin
register_activation_hook(__FILE__,  array('\Qdiscuss\Dashboard\Bridge', 'activate'));
register_deactivation_hook(__FILE__,  array('\Qdiscuss\Dashboard\Bridge', 'deactivate'));
register_uninstall_hook(__FILE__,  array('\Qdiscuss\Dashboard\Bridge', 'uninstall'));

// QDiscuss Hook-ups
add_action('wp_login', '\Qdiscuss\Dashboard\Bridge::hook_user_login');
add_action('user_register', '\Qdiscuss\Dashboard\Bridge::hook_user_register');
add_action('delete_user', '\Qdiscuss\Dashboard\Bridge::hook_user_delete');
add_action('profile_update', '\Qdiscuss\Dashboard\Bridge::hook_profile_update');
add_action('edit_user_profile_update', '\Qdiscuss\Dashboard\Bridge::hook_user_profile_update');

// Dashboard Setting hook-ups
add_action( 'admin_init', array('\Qdiscuss\Dashboard\QdInstall', 'check_version'), 5 );
add_action( 'admin_init',  array('\Qdiscuss\Dashboard\QdInstall', 'install_actions'));
add_action( 'wp_loaded', array('\Qdiscuss\Dashboard\AdminNotices', 'hide_notices' ) );		
add_action( 'admin_print_styles', array('\Qdiscuss\Dashboard\AdminNotices', 'add_notices' ) );
add_action( 'admin_enqueue_scripts', '\Qdiscuss\Dashboard\Dashboard::qdiscuss_enqueue_admin');
add_action( 'admin_menu',     '\Qdiscuss\Dashboard\Dashboard::qdiscuss_admin_menu', 999 );
add_action('wp_ajax_qdiscuss_ajax_config_settings_save', '\Qdiscuss\Dashboard\Dashboard::qdiscuss_ajax_config_settings_save');
add_action('admin_init',   '\Qdiscuss\Dashboard\Dashboard::qdiscuss_admin_init', 999 );

// function json_api_init() {
// 	\Qdiscuss\Router::register_rewrite();
// 	global $wp;
// 	$wp->add_query_var( 'json_route' );
// }
// add_action( 'init', 'json_api_init' );


/*
|--------------------------------------------------------------------------
| Register REST API For Web Client
|--------------------------------------------------------------------------
|
*/
$wprestfy_class = apply_filters( 'wp_json_server_class', '\Qdiscuss\Toro' );
add_action( 'init', $wprestfy_class::serve(\Qdiscuss\Router::routes()));


