<?php
/*
Plugin Name: QDiscuss
Plugin URI: http://colorvila.com/qdiscuss-plugin
Description: Next-generation Forum Plugin for WordPress
Version: 0.4.5
Author: ColorVila Team
Author URI: http://colorvila.com/qdiscuss-plugin
*/
if(PHP_VERSION < '5.4') die(' PHP_VERSION need >= 5.4, please upgrade your PHP');

const QDISCUSS_VERSION = '0.4.5';
define('QDISCUSS_URI', plugin_dir_url( __FILE__));

require __DIR__ . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Load the global config
|--------------------------------------------------------------------------
|
*/
require_once __DIR__.'/bootstrap/config.php';
require_once __DIR__.'/bootstrap/db.php';

/*
|--------------------------------------------------------------------------
| Register Some Helper Functions
|--------------------------------------------------------------------------
|
*/
require __DIR__.'/bootstrap/helper.php';

/*
|--------------------------------------------------------------------------
| Launch The QDiscuss Application
|--------------------------------------------------------------------------
|
*/
require __DIR__.'/bootstrap/app.php';

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
add_action( 'wp_loaded', array('\Qdiscuss\Dashboard\Notices', 'hide_notices' ) );			
add_action( 'admin_print_styles', array('\Qdiscuss\Dashboard\Notices', 'add_notices' ) );
add_action('delete_user', '\Qdiscuss\Dashboard\Bridge::hook_user_delete');
add_action('profile_update', '\Qdiscuss\Dashboard\Bridge::hook_profile_update');
add_action('edit_user_profile_update', '\Qdiscuss\Dashboard\Bridge::hook_user_profile_update');

// Dashboard Setting hook-ups
add_action( 'admin_init', array('\Qdiscuss\Dashboard\Install', 'check_version'), 5 );
add_action( 'admin_init',  array('\Qdiscuss\Dashboard\Install', 'install_actions'));
add_action( 'admin_enqueue_scripts', '\Qdiscuss\Dashboard\Dashboard::qdiscuss_enqueue_admin');
add_action( 'admin_menu',     '\Qdiscuss\Dashboard\Dashboard::qdiscuss_admin_menu', 999 );
add_action('wp_ajax_qdiscuss_ajax_config_settings_save', '\Qdiscuss\Dashboard\Dashboard::qdiscuss_ajax_config_settings_save', 999);
add_action('wp_ajax_qdiscuss_ajax_roles_settings_save', '\Qdiscuss\Dashboard\Dashboard::qdiscuss_ajax_roles_settings_save', 999);
add_action('wp_ajax_qdiscuss_ajax_extensions_settings_save', '\Qdiscuss\Dashboard\Dashboard::qdiscuss_ajax_extensions_settings_save', 999);
add_action('admin_init',   '\Qdiscuss\Dashboard\Dashboard::qdiscuss_admin_init', 999 );

/*
|--------------------------------------------------------------------------
| Register REST API For Web Client
|--------------------------------------------------------------------------
|
*/
// Load Routes
include(__DIR__ . '/heart/routes.php');


