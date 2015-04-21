<?php
/*
Plugin Name: QDiscuss
Plugin URI: 
Description: An Amazing Forum WordPress Plugin
Version: 0.0.2
Author: ColorVila Team
Author URI: http://colorvila.com/about-qdiscuss
*/
if(PHP_VERSION < '5.4') die(' PHP_VERSION need >= 5.4, please upgrade your PHP');

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

/*
|--------------------------------------------------------------------------
| Register REST API For Web Client
|--------------------------------------------------------------------------
|
*/
// Home Page
add_filter('json_endpoints', array(\Qdiscuss\Forum\Routers\IndexRouter::router(), 'register_routes'));
// Sessions
add_filter('json_endpoints', array(\Qdiscuss\Forum\Routers\SessionRouter::router(), 'register_routes'));
// Discussions
add_filter('json_endpoints', array(\Qdiscuss\Api\Routers\DiscussionRouter::router(), 'register_routes'));
// Posts
add_filter('json_endpoints', array(\Qdiscuss\Api\Routers\PostRouter::router(), 'register_routes'));
//Activity
add_filter('json_endpoints', array(\Qdiscuss\Api\Routers\ActivityRouter::router(), 'register_routes'));
//Users
add_filter('json_endpoints', array(\Qdiscuss\Api\Routers\UserRouter::router(), 'register_routes'));
//Notification
add_filter('json_endpoints', array(\Qdiscuss\Api\Routers\NotificationRouter::router(), 'register_routes'));
//Admin
add_filter('json_endpoints', array(\Qdiscuss\Admin\Routers\AdminRouter::router(), 'register_routes'));

 /*
 |--------------------------------------------------------------------------
 | Hook-ups into WordPress Core
 |--------------------------------------------------------------------------
 |
 */
// QDiscuss Plugin
register_activation_hook(__FILE__,  array('Qdiscuss', 'activate'));
register_deactivation_hook(__FILE__,  array('Qdiscuss', 'deactivate'));
register_uninstall_hook(__FILE__,  array('Qdiscuss', 'uninstall'));

// QDiscuss Hook-ups
add_action('wp_json_server_before_serve', array('Qdiscuss', 'init'));
add_action( 'init', '\Qdiscuss\Dashboard\Bridge::register_rewrite');
add_action('wp_login', '\Qdiscuss\Dashboard\Bridge::hook_user_login');
add_action('user_register', '\Qdiscuss\Dashboard\Bridge::hook_user_register');
add_action('delete_user', '\Qdiscuss\Dashboard\Bridge::hook_user_delete');
add_action('profile_update', '\Qdiscuss\Dashboard\Bridge::hook_profile_update');
add_action('edit_user_profile_update', '\Qdiscuss\Dashboard\Bridge::hook_user_profile_update');

// Dashboard Setting hook-ups
add_action( 'admin_enqueue_scripts', '\Qdiscuss\Dashboard\Dashboard::qdiscuss_enqueue_admin');
add_action( 'admin_menu',     '\Qdiscuss\Dashboard\Dashboard::qdiscuss_admin_menu', 999 );
add_action('wp_ajax_qdiscuss_ajax_config_settings_save', '\Qdiscuss\Dashboard\Dashboard::qdiscuss_ajax_config_settings_save');
add_action('admin_init',   '\Qdiscuss\Dashboard\Dashboard::qdiscuss_admin_init', 999 );

//Install Require Rest Json Api plugin
require_once __DIR__ . '/heart/Dashboard/plugin-activate.php';
add_action( 'tgmpa_register', '\Qdiscuss\Dashboard\Bridge::qdiscuss_register_required_plugins' );