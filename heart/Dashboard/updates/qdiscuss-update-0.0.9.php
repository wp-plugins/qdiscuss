<?php
	// move the extensions directory to wp-content.qdiscuss
	\Qdiscuss\Dashboard\Bridge::create_qd_dir();
	// Add column is_deleted to notifications table;
	global $wpdb;
	$prefix = \Illuminate\Database\Capsule\Manager::getTablePrefix();
	$wpdb->query("ALTER TABLE `" . $prefix . 'notifications` ADD COLUMN `is_deleted` tinyint(1) NOT NULL DEFAULT 0'); 
	// Change the activity table column sender_id rename to subject_id
	$wpdb->query("ALTER TABLE `" . $prefix . 'activity` CHANGE COLUMN `sender_id` `subject_id` int(10) unsigned DEFAULT NULL'); 
?>