<?php
use Illuminate\Database\Capsule\Manager as DB; 
	// todo upgrade the group table
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $wpdb;
	$wpdb->query('drop table ' . $wpdb->prefix . 'qd_groups');
	$sql = file_get_contents(__DIR__ . "/../migrations/"  . "groups-schema.sql");
	$new_sql = preg_replace("/_qdiscuss_prefix_/", $wpdb->prefix . 'qd_', $sql);
	dbDelta($new_sql);
	\Qdiscuss\Dashboard\Bridge::seed_groups();

	// upgrade the config table for extension information
	$plugins = '';
	$wpdb->insert( 
		$wpdb->prefix . 'qd_config', 
		array( 
			'key' => 'extensions_enabled', 
			'value' =>  $plugins 
		), 
		array( 
			'%s', 
			'%s' 
		) 
	);

	// simple the premission api
	DB::statement('drop table ' . $wpdb->prefix . 'qd_permissions');
	$permission_sql = file_get_contents(__DIR__."/../migrations/" . "permissions-schema.sql");
	DB::statement(preg_replace("/_qdiscuss_prefix_/", $wpdb->prefix . 'qd_', $permission_sql));
	\Qdiscuss\Dashboard\Bridge::seed_permissions();
	\Qdiscuss\Dashboard\Bridge::create_cache_dir();