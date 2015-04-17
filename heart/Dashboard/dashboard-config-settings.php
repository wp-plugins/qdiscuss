 <div class="wrap">
 	<h2>QDiscuss Settings</h2>

 	<p><a href="<?php echo admin_url() . 'admin.php?page=qdiscuss-settings';?>">Back</a></p>
 	<form id="qdiscuss-settings-form" name="qdiscuss-settings-form" action="" enctype="multipart/form-data" method="POST">
 		
		<p><strong><?php _e('Key'); ?>:</strong><?php echo $key; ?><br>
		<?php 
			if($key == 'forum_endpoint') : 
				echo '<span style="color:red;">Should start and end with (a-zA-Z) letters, only contain (a-zA-Z), -,  and length less than 10.</span>';
			endif;
		?>
		</p>
	 	
	 	<p><strong><?php _e('Value'); ?>:</strong>
		 	<input type="text" size="50" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
		</p>
	 	
	 	<p class="clear"></p>
	 	<p ><div id="save-config-setting" class="button button-primary">Save</div></p>
	 	
	</form>

</div>