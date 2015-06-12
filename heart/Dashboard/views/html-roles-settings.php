<?php include('html-header.php'); ?>

<div class="wrap  qd-wrap">

 	<h3>Role Setting</h3>

 	<p><a href="<?php echo admin_url() . 'admin.php?page=qdiscuss-users';?>">Back</a></p>
 	<form id="qdiscuss-roles-settings-form" name="qdiscuss-roles-settings-form" action="" enctype="multipart/form-data" method="POST">
 		<h2><?php echo $user->username ? $user->username : $user->user_login; ?></h2>

 		<p><strong><?php _e('Email'); ?>:</strong><?php echo $user->email ? $user->email : $user->user_email; ?></p>
	 	
	 	<p><strong><?php _e('Role'); ?>:</strong>
		 	<select name="qdiscuss_group">
		 		<?php if(!$user_id) :
		 			echo '<option value ="0">None</option>';?>
		 		<?php endif;?>
		 		<?php foreach($qdiscuss_roles as $role) :   ?>
		 			<?php if($role['id'] == 2 || $role['id'] == 5)  continue; ?>
		 			<option value ="<?php echo $role['id']; ?>" <?php if($user->groups[0]->id == $role['id']) echo 'selected="selected"'; ?>><?php echo $role['name_singular']; ?></option>
		 		<?php endforeach;  ?>   			
		 	</select>
		</p>
		<p>
			<input type="hidden" name='user_id' value='<?php echo $user_id;?>' />
			<input type="hidden" name='wp_user_id' value='<?php echo $wp_user_id;?>' />
		</p>
	 	
	 	<p class="clear"></p>
	 	<p ><div id="save-roles-config-setting" class="button button-primary">Save</div></p>
	</form>
</div>

