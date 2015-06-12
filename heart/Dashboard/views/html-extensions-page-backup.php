<script>
	var installedExtensions = <?php echo json_encode($extensions); ?>;
</script>
<?php include('html-header.php'); ?>

	<div id='installed-extensions' class="wrap  qd-wrap">
		<h3>Installed Extensions</h3>
		<hr>
	 	<table class="form-table">
	 	<?php if($extensions) :?>
	 		<tr>
	 			<th><?php _e('Name'); ?></th>
	 			<th><?php _e('Description'); ?></th>
	 			<th><?php _e('Version'); ?></th>
	 			<th><?php _e('Apply'); ?></th>
	 		</tr>
	 		<?php foreach ($extensions as $key=>$extension) :?>
		 	<tr>
			 	<td><?php echo $extension['name']; ?></td>
			         	<td><?php echo $extension['description'];?></td>
			         	<td>
			         		<?php echo $extension['version'];
			         		if ($extension['version'] <= '0.0.3') : 
			         			echo '';
			         		elseif ($extension['status'] == 2) :
			         			if($max_version = $extension['require']['Qdiscuss']['max']) :
				         			$version_content =  ' (Need QDiscuss version >= ' . $extension['require']['Qdiscuss']['min'];
				         			$version_content .=  ' and <= ' .  $max_version;
				         			$version_content .= ')';
						else :
							$version_content = '(Need QDiscuss version == ' . $extension['require']['Qdiscuss']['min'] . ')';
						endif;
						echo $version_content;
					endif;
					?>
			         	</td>
			             <td>
			             		<?php if($extension['version'] <= '0.0.3') : 
			         			echo 'Please update to 0.0.4'; ?>
				              <?php elseif ($extension['status'] == 0) :?>
				              	<a class="save-extensions-setting" data-setting-data="setting_method=activate&extension_name=<?php echo $key; ?>" href="#"><?php echo 'Activate'; ?></a>
				             <?php elseif ($extension['status'] == 1) : ?>
				              	<a class="save-extensions-setting"  data-setting-data="setting_method=deactivate&extension_name=<?php echo $key; ?>" href="#"><?php echo 'Deactivate'; ?></a>
				             <?php // @todo add remove button if nessary ?>
					<?php elseif ($extension['status'] == 2) : ?>
						<a class=""><?php echo 'Your current QDisucss version is ' . QDISCUSS_VERSION. ', please check the extension and QDiscuss version, and update them' ; ?></a>
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
	 	<p>* To INSTALL extension, just unzip the extension then move it into the <strong>wp-content/qdiscuss/extensions</strong> directory.</p>
	 	<p>If you have some install problems, please go to <a href="http://colorvila.com/qdiscuss" target='blank'>our official QDiscuss forum</a> to start a discussion under Feedback category.</p>

	 	<!-- <iframe id='qdiscuss-main' src="http://colorvila.com/themes" width="100%" height="1000px"  min-height="750px" frameborder="0" scrolling="yes"></iframe> -->
		
		<?php include('html-footer.php'); ?>
	</div>
