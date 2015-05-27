<?php
	// Change the table's field's size
	global $wpdb;
	$prefix = \Illuminate\Database\Capsule\Manager::getTablePrefix();

	$alter_table_sqls = array(
		'ALTER TABLE `' . $prefix . 'access_tokens` MODIFY `id` varchar(100) NOT NULL',
		'ALTER TABLE `' . $prefix . 'access_tokens` ADD primary key (id)',
		'ALTER TABLE `' . $prefix . 'discussions` MODIFY title varchar(200) NOT NULL',
		'ALTER TABLE `' . $prefix . 'groups` MODIFY `name_singular` varchar(100) NOT NULL',
		'ALTER TABLE `' . $prefix . 'groups` MODIFY `name_plural` varchar(100) NOT NULL',
		'ALTER TABLE `' . $prefix . 'groups` MODIFY `color` varchar(20) DEFAULT NULL',
		'ALTER TABLE `' . $prefix . 'groups` MODIFY `icon` varchar(100) DEFAULT NULL',
		'ALTER TABLE `' . $prefix . 'notifications` MODIFY `type` varchar(100) NOT NULL',
		'ALTER TABLE `' . $prefix . 'notifications` MODIFY `subject_type` varchar(200) DEFAULT NULL',
		'ALTER TABLE `' . $prefix . 'posts` MODIFY `type` varchar(100) DEFAULT NULL',
		'ALTER TABLE `' . $prefix . 'users` MODIFY `username` varchar(100) NOT NULL',
		'ALTER TABLE `' . $prefix . 'users` MODIFY `email` varchar(100) NOT NULL',
		'ALTER TABLE `' . $prefix . 'users` MODIFY `confirmation_token` varchar(50) DEFAULT NULL',
		'ALTER TABLE `' . $prefix . 'users` MODIFY `password` varchar(100) NOT NULL',
		'ALTER TABLE `' . $prefix . 'users` MODIFY `avatar_path` varchar(100) DEFAULT NULL',
	);

	foreach ($alter_table_sqls as $sql) {
		$wpdb->query($sql);
	}

?>