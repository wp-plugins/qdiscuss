<?php namespace Qdiscuss\Dashboard;

class Notices {

	/**
	 * Array of notices - name => callback
	 * 
	 * @var array
	 */
	static  $notices = array(
		//'install'             => 'install_notice',
		'update'              => 'update_notice',		
	);

	
	/**
	 * Show a notice
	 * 
	 * @param  string $name
	 */
	public static function add_notice( $name ) 
	{
		$notices = array_unique( array_merge( get_option( 'qdiscuss_admin_notices', array() ), array( $name ) ) );
		update_option( 'qdiscuss_admin_notices', $notices );
	}

	/**
	 * Remove a notice from being displayed
	 * 
	 * @param  string $name
	 */
	public static function remove_notice( $name ) 
	{
		$notices = array_diff( get_option( 'qdiscuss_admin_notices', array() ), array( $name ) );
		update_option( 'qdiscuss_admin_notices', $notices );
	}

	/**
	 * See if a notice is being shown
	 * 
	 * @param  string  $name
	 * @return   boolean
	 */
	public static function has_notice( $name ) 
	{
		return in_array( $name, get_option( 'qdiscuss_admin_notices', array() ) );
	}

	/**
	 * Hide a notice if the GET variable is set.
	 */
	public function hide_notices() 
	{
		if ( isset( $_GET['qd-hide-notice'] ) ) {
			$hide_notice = sanitize_text_field( $_GET['qd-hide-notice'] );
			self::remove_notice( $hide_notice );
			do_action( 'qdiscuss_hide_' . $hide_notice . '_notice' );
		}
	}

	/**
	 * When install is hidden, trigger a redirect
	 */
	// public function hide_install_notice() {
	// 	// What's new redirect
	// 	if ( ! self::has_notice( 'update' ) ) {
	// 		delete_transient( '_qd_activation_redirect' );
	// 		wp_redirect( admin_url( 'index.php?page=wc-about&wc-updated=true' ) );
	// 		exit;
	// 	}
	// }

	
	/**
	 * Add notices + styles if needed.
	 */
	public function add_notices() 
	{
		$notices = get_option( 'qdiscuss_admin_notices', array() );

		foreach ( $notices as $notice ) {			
			add_action( 'admin_notices', array('\Qdiscuss\Dashboard\Notices', self::$notices[ $notice ]));
		}
	}

	/**
	 * If we need to update, include a message with the update button
	 */
	public function update_notice() 
	{
		include( 'views/html-notice-update.php' );
	}

	/**
	 * If we have just installed, show a message with the install pages button
	 */
	// public function install_notice() {
	// 	include( 'views/html-notice-install.php' );
	// }
	
}

