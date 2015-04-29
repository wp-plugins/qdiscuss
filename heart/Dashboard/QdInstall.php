<?php  namespace Qdiscuss\Dashboard;

/**
 * QdInstall Class
 */
class QdInstall {

	/**
	 * check_version function.
	 */
	public static function check_version() {
		
		if ( ( get_option( 'qdiscuss_version' ) != QDISCUSS_VERSION || get_option( 'qdiscuss_db_version' ) != QDISCUSS_VERSION ) ) {
			self::install();
			do_action( 'qdiscuss_updated' );
		}
	}

	/**
	 * Install actions.
	 */
	public static function install_actions() {

		if ( ! empty( $_GET['do_update_qdiscuss'] ) ) {

			self::update();

			// Update complete
			AdminNotices::remove_notice( 'update' );

			// What's new redirect
			// if ( ! AdminNotices::has_notice( 'install' ) ) {
			// 	delete_transient( '_qd_activation_redirect' );
			// 	wp_redirect( admin_url( 'index.php?page=qd-about&qd-updated=true' ) );
			// 	exit;
			// }
		}
	}

	/**
	 * Install WC
	 */
	public static function install() {
		
		// Ensure needed classes are loaded
		include_once( 'AdminNotices.php' );
		
		// Queue upgrades
		$current_db_version = get_option( 'qdiscuss_db_version', null );

		if ( version_compare( $current_db_version, QDISCUSS_VERSION, '<' )  || null == $current_db_version ) {
			AdminNotices::add_notice( 'update' );
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
	private static function update() {
		$current_db_version = get_option( 'qdiscuss_db_version' );
		$db_updates         = array(
			'0.0.3' => 'updates/qdiscuss-update-0.0.3.php',			
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
