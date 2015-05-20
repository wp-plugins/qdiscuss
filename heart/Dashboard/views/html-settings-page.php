<?php  include('html-header.php'); ?>
	<div class="wrap qd-wrap">
		<h3>QDiscuss General Setting</h3>

		<p>QDiscuss is a modern designed, well-architected, powerful forum plugin that is easy to use,  with which you can easily add a forum to your site and allow your members to start their own conversations. </p>
		
		<p>Now Directly access to <a target="blank" href="<?php echo get_site_url() . '/' . $endpoint; ?>">Your QDiscuss Now!</a></p>

		<hr>
		<table class="form-table">
			<tr>
				<th><?php _e('Key'); ?></th>
				<th><?php _e('Value'); ?></th>
				<th><?php _e('Apply'); ?></th>
			</tr>
			<?php foreach ($settings as $key => $value) :?>
			<tr>
				<td><?php echo $key; ?></td>
				<td><?php echo $value; ?></td>
				<td><a href="<?php echo admin_url() . 'admin.php?page=qdiscuss-config-settings&key=' . $key; ?>"><?php _e('Edit'); ?></a></td>
			</tr>
			<?php endforeach; ?>
		</table>
		<?php include('html-footer.php'); ?>
	</div>
