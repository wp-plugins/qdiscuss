<?php
/**
 * Admin View: Notice - Update
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div id="message" class="updated qdiscuss-message qd-connect">
	<p><?php _e( '<strong>QDiscuss Data Update Required</strong> &#8211; We just need to update your install to the latest version', 'qdiscuss' ); ?></p>
	<p class="submit"><a href="<?php echo esc_url( add_query_arg( 'do_update_qdiscuss', 'true', admin_url( 'admin.php?page=qdiscuss-settings' ) ) ); ?>" class="qd-update-now button-primary"><?php _e( 'Run the updater', 'qdiscuss' ); ?></a></p>
</div>
<script type="text/javascript">
	jQuery('.qd-update-now').click('click', function(){
		var answer = confirm( '<?php _e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'qdiscuss' ); ?>' );
		return answer;
	});
</script>
