<?php include('html-header.php'); ?>

<div class="container">
	<br>
	<br>
	<div class="well col-lg-10">
		<a class="pull-right" href="<?php echo admin_url() . 'admin.php?page=qdiscuss-users';?>"><i class="mdi-content-clear"></i></a>
	 	<form id="qdiscuss-roles-settings-form"  class="form-horizontal" name="qdiscuss-roles-settings-form" action="" enctype="multipart/form-data" method="POST">
	 		<fieldset>
	 			<div class="form-group">
	 			            <label for="inputName" class="col-lg-2 control-label"><?php _e('Name'); ?></label>
	 			            <div class="col-lg-4">
	 			            		<input type="text" class="form-control" id="inputName" disabled value="<?php echo $user->username ? $user->username : $user->user_login; ?>">
	 			            </div>
	 			</div>

	 			<div class="form-group">
	 			            <label for="inputEmail" class="col-lg-2 control-label"><?php _e('Email'); ?></label>
	 			            <div class="col-lg-4">
	 			            		<input type="text" class="form-control" id="inputEmail" disabled value="<?php echo $user->email ? $user->email : $user->user_email; ?>">
	 			            </div>
	 			</div>

				<div class="form-group">
				            <label for="inputRole" class="col-lg-2 control-label"><?php _e('Role'); ?></label>
				            <div class="col-lg-4">
				            		<select name="qdiscuss_group" class="form-control">
				            			<?php if(!$user_id) :
				            				echo '<option value ="0">None</option>';?>
				            			<?php endif;?>
				            			<?php foreach($qdiscuss_roles as $role) :   ?>
				            				<?php if($role['id'] == 2 || $role['id'] == 5)  continue; ?>
				            				<option value ="<?php echo $role['id']; ?>" <?php if($user->groups[0]->id == $role['id']) echo 'selected="selected"'; ?>><?php echo $role['name_singular']; ?></option>
				            			<?php endforeach;  ?>   			
				            		</select>
				            </div>
				</div>
				<p>
					<input type="hidden" name='user_id' value='<?php echo $user_id;?>' />
					<input type="hidden" name='wp_user_id' value='<?php echo $wp_user_id;?>' />
				</p>
		 	
		 		<div class="form-group">
		 		            <div class="col-lg-10 col-lg-offset-2">
		 		           		<a class="btn btn-default" href="<?php echo admin_url() . 'admin.php?page=qdiscuss-users';?>"><?php _e('Cancel'); ?></a>
		 		            		<button id="save-roles-config-setting"  type="submit" class="btn btn-primary"><?php _e('Save'); ?></button>
		 		            </div>
		 		</div>
		</form>
	</div>
</div>

