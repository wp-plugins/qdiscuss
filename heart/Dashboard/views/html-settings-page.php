	<div class="">
		<h2><?php echo _e("QDiscuss General Setting"); ?></h2>
		<hr>
		<p>QDiscuss is a modern designed, well-architected, powerful forum plugin that is easy to use,  with which you can easily add a forum to your site and allow your members to start their own conversations. </p>
		<p>Now Directly access to <a target="blank" href="<?php echo get_site_url() . '/' . $endpoint; ?>">Your QDiscuss Now!</a></p>
		<br>
		<div class="">
			<table class="table table-hover">
				<tr>
					<th><?php _e('Key'); ?></th>
					<th><?php _e('Value'); ?></th>
					<th><?php _e('Apply'); ?></th>
				</tr>
				<?php foreach ($settings as $key => $value) :?>
				<tr>
					<td><?php echo $key; ?></td>
					<td>
						<?php 
							echo $value;
							if ($key == 'forum_language') echo ' (Contribute to <a href="https://github.com/ColorVila/QDiscuss-languanges">Translation Project of QDiscuss</a>)' ;
						?>
					</td>
					<td><a class="btn btn-primary" href="<?php echo admin_url() . 'admin.php?page=qdiscuss-config-settings&key=' . $key; ?>"><?php _e('Edit'); ?></a></td>
				</tr>
				<tr></tr>
				<?php endforeach; ?>
			</table>
		</div>
		<?php include('html-footer.php'); ?>
	</div>
