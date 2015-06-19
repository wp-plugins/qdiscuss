<?php  namespace Qdiscuss\Dashboard;

class Install {

	/**
	 * Check_version function.
	 */
	public static function check_version() 
	{
		if(get_option( 'qdiscuss_version' )) {
			if ( ( get_option( 'qdiscuss_version' ) != QDISCUSS_VERSION || get_option( 'qdiscuss_db_version' ) != QDISCUSS_VERSION ) ) {
				self::install();
				do_action( 'qdiscuss_updated' );
			}
		}
	}

	/**
	 * Install actions.
	 */
	public static function install_actions() 
	{

		if ( ! empty( $_GET['do_update_qdiscuss'] ) ) {

			self::update();

			// Update complete
			Notices::remove_notice( 'update' );

			// What's new redirect
			// if ( ! WC_Admin_Notices::has_notice( 'install' ) ) {
			// 	delete_transient( '_wc_activation_redirect' );
			// 	wp_redirect( admin_url( 'index.php?page=wc-about&wc-updated=true' ) );
			// 	exit;
			// }
		}
	}

	/**
	 * Install 
	 */
	public static function install() 
	{
		global $wpdb, $qdiscuss_config;

		$table_name = $qdiscuss_config['database']['prefix'] . 'config';
		$current_db_version = get_option( 'qdiscuss_db_version', null );
		$is_new_install = ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name);

		if(!$is_new_install && is_null($current_db_version)){
			Notices::add_notice( 'update' );
		}elseif ($current_db_version &&  version_compare( $current_db_version, QDISCUSS_VERSION, '<' )  ) {
			Notices::add_notice( 'update' );
		} else {
			delete_option( 'qdiscuss_db_version' );
			add_option( 'qdiscuss_db_version', QDISCUSS_VERSION );
		}

		// Update version
		delete_option( 'qdiscuss_version' );
		add_option( 'qdiscuss_version', QDISCUSS_VERSION );

		// Trigger action
		do_action( 'qdiscuss_installed' );
	}

	/**
	 * Handle updates
	 */
	protected static function update() 
	{
		$current_db_version = get_option( 'qdiscuss_db_version' );
		$db_updates = array(
			'0.0.3' => 'updates/qdiscuss-update-0.0.3.php',
			'0.0.6' => 'updates/qdiscuss-update-0.0.6.php',
			'0.0.8' => 'updates/qdiscuss-update-0.0.8.php',
			'0.0.9' => 'updates/qdiscuss-update-0.0.9.php',
			'0.2'    => 'updates/qdiscuss-update-0.2.php',
			'0.4'    => 'updates/qdiscuss-update-0.4.php',
			'0.4.4'    => 'updates/qdiscuss-update-0.4.4.php',
		);

		foreach ( $db_updates as $version => $updater ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				include( $updater );
				delete_option( 'qdiscuss_db_version' );
				add_option( 'qdiscuss_db_version', $version );
			}
		}

		delete_option( 'qdiscuss_db_version' );
		add_option( 'qdiscuss_db_version', QDISCUSS_VERSION );
	}

}
