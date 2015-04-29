<?php
global $wpdb;
$wpdb->insert( 
	$wpdb->prefix . 'qd_config', 
	array( 
		'key' => 'forum_welcome_title', 
		'value' =>  'Welcome to QDiscuss' 
	), 
	array( 
		'%s', 
		'%s' 
	) 
);