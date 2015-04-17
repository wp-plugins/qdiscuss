<?php
global $wpdb;

return array(
	"database" => array(
		'qd_prefix' => 'qd_',
		'driver'         => 'mysql',
		'host'           => DB_HOST,
		'database'    => DB_NAME,
		'username'  => DB_USER,
		'password'   => DB_PASSWORD,
		'charset'      => DB_CHARSET,
		//'collation'    => 'utf8_unicode_ci',
		'prefix'        => $wpdb->prefix .  QD_PREFIX,
	),
);

