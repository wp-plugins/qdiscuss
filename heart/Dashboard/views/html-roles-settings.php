<?php include('html-header.php'); ?>

<div class="wrap  qd-wrap">

 	<h3>Role Setting</h3>

 	<p><a href="<?php echo admin_url() . 'admin.php?page=qdiscuss-users';?>">Back</a></p>
 	<form id="qdiscuss-settings-form" name="qdiscuss-settings-form" action="" enctype="multipart/form-data" method="POST">
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
	 	
	 	<p class="clear"></p>
	 	<p class="submit"><input class="button button-primary" type="submit" value="Save" ></input></p>
	</form>
</div>

