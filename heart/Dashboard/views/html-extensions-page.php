<?php include('html-header.php'); ?>

	<div class="wrap  qd-wrap">
		<h3>Installed Extensions</h3>
		<hr>

	 	<table class="form-table">
	 	<?php if($extensions) :?>
	 		<tr>
	 			<th><?php _e('Name'); ?></th>
	 			<th><?php _e('Description'); ?></th>
	 			<th><?php _e('Apply'); ?></th>
	 		</tr>
	 		<?php foreach ($extensions as $key=>$extension) :?>
		 	<tr>
			 	<td><?php echo $extension['name']; ?></td>
			         	<td><?php echo $extension['description'];?></td>
			              <td>
				              <?php if ($extension['is_activated'] == 0) :?>
				              	<a class="save-extensions-setting" data-setting-data="setting_method=activate&extension_name=<?php echo $key; ?>" href="#"><?php echo 'Activate'; ?></a>
				              <?php else : ?>
				              	<a class="save-extensions-setting"  data-setting-data="setting_method=deactivate&extension_name=<?php echo $key; ?>" href="#"><?php echo 'Deactivate'; ?></a>
				              	
				              <?php endif ;?>
			              </td>
			</tr>
		 	<?php endforeach; ?>
	 	<?php else :?>
			<p>No extension installed yet! Browser <a target='blank' href="http://colorvila.com/qdiscuss-extensions/">All our Extensions</a> for QDiscuss now!</p>
	 	<?php endif;?>
	 	</table>

	 	<h3>Browser All Extensions</h3>
	 	<hr>
	 	<p>Go to our <a  target="blank" href="http://colorvila.com/qdiscuss-extensions/">official extensions gallery</a>.</p>
	 	<p>* To INSTALL extension, just unzip the extension then move it into the <strong>wp-content/plugins/qdiscuss/extensions</strong> directory.

	 	<!-- <iframe id='qdiscuss-main' src="http://colorvila.com/themes" width="100%" height="1000px"  min-height="750px" frameborder="0" scrolling="yes"></iframe> -->
		
		<?php include('html-footer.php'); ?>
	</div>