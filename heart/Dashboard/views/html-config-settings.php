<div class="container">
	<br>
	<br>
	<div class="well col-lg-10">
		<a class="pull-right" href="<?php echo admin_url() . 'admin.php?page=qdiscuss-settings';?>"><i class="mdi-content-clear"></i></a>
	 	<form id="qdiscuss-settings-form" class="form-horizontal" name="qdiscuss-settings-form" action="" enctype="multipart/form-data" method="POST">
	 		<fieldset>
	 			<div class="form-group">
	 				<label for="inputKey" class="col-lg-2 control-label"><?php _e('Key'); ?></label>
	 				<div class="col-lg-4">
	 			            		<input type="text" class="form-control" id="inputEmail" disabled placeholder="<?php echo $key; ?>">
	 			              </div>
	 			</div>
			 	<div class="form-group">
			 		<label for="inputValue" class="col-lg-2 control-label"><?php _e('Value'); ?></label>
			 		<div class="col-lg-4">
			 	             		<?php 
			 	             			if ($key == 'forum_language') :
			 	             				echo '<select class="form-control" name="forum_language">';
			 	             				foreach ($language_files as $language) :
			 	             					if ($language == $set_value) echo '<option selected value="' . $language . '">' . $language .'</option>';
			 	             					else echo '<option value="' . $language . '">' . $language .'</option>';
			 	             				endforeach;
			 	             				echo '</select>';
			 	             			elseif ($key == 'forum_description') :
			 	             				echo "<textarea class='form-control' rows='10'  col='100' name=" .$key . " >" . $set_value . "</textarea>";
			 	             			else :
			 	             				echo "<input type='text' class='form-control' name=" . $key . " value=" . $set_value . " />";
			 	             			endif;
			 	             		?>
			 	             		</p>
			 	             		<p>
			 	             		<?php 
			 	             			if($key == 'forum_endpoint') : 
			 	             				echo '<span style="color:red;">Should start and end with (a-zA-Z) letters, only contain (a-zA-Z), -,  and length less than 10.</span>';
			 	             			endif;
			 	             		?>
			 	             		</p>
			 	            	</div>
			 	</div>
				
			 	<div class="form-group">
			 		<div class="col-lg-10 col-lg-offset-2">
			 	           		<a href="<?php echo admin_url() . 'admin.php?page=qdiscuss-settings';?>" class="btn btn-default"><?php _e('Cancel'); ?></a>
			 	            		<button id="save-config-setting" type="submit" class="btn btn-primary"><?php _e('Save'); ?></button>
			 		</div>
			 	</div>
		 	</fieldset>
		</form>
	</div>
</div>

