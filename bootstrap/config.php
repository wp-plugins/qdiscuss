<?php
global $wpdb, $qdiscuss_config;

return $qdiscuss_config = array(
	"database" => array(
		'fetch'    => PDO::FETCH_CLASS,
		'default' => 'mysql',
		'driver' => 'mysql',
		'host'           => DB_HOST,
		'database'    => DB_NAME,
		'username'  => DB_USER,
		'password'   => DB_PASSWORD,
		'charset'      => DB_CHARSET,
		//'collation'    => 'utf8_unicode_ci',
		'prefix'        => $wpdb->prefix .  'qd_',
		'qd_prefix'  => 'qd_',
	)
);