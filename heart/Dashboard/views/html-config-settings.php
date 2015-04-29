 <div class="wrap">
 	<h2>QDiscuss Settings</h2>

 	<p><a href="<?php echo admin_url() . 'admin.php?page=qdiscuss-settings';?>">Back</a></p>
 	<form id="qdiscuss-settings-form" name="qdiscuss-settings-form" action="" enctype="multipart/form-data" method="POST">
 		
		<p><strong><?php _e('Key'); ?>:</strong></p>
		<p><?php echo $key; ?></p>		
	 	
	 	<p><strong><?php _e('Value'); ?>:</strong></p>
		<p><textarea name="<?php echo $key; ?>" rows="2" cols="30" ><?php echo esc_attr($value); ?></textarea></p>
		<p>
		<?php 
			if($key == 'forum_endpoint') : 
				echo '<span style="color:red;">Should start and end with (a-zA-Z) letters, only contain (a-zA-Z), -,  and length less than 10.</span>';
			endif;
		?>
		</p>
		
	 	
	 	<p class="clear"></p>
	 	<p ><div id="save-config-setting" class="button button-primary">Save</div></p>
	 	
	</form>

</div>