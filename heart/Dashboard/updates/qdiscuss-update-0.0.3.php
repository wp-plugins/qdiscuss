<?php
global $wpdb, $qdiscuss_config;
$wpdb->insert( 
	$qdiscuss_config['database']['prefix'] . 'config', 
	array( 
		'key' => 'forum_welcome_title', 
		'value' =>  'Welcome to QDiscuss' 
	), 
	array( 
		'%s', 
		'%s' 
	) 
);