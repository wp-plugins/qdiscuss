 <div class="wrap">
 	<h2>QDiscuss Settings</h2>

 	<p>QDiscuss is a modern designed, well-architected, powerful forum plugin that is easy to use,  with which you can easily add a forum to your site and allow your members to start their own conversations. </p>
 	
 	<p>Now Directly access to <a target="blank" href="<?php echo get_site_url() . '/' . $endpoint; ?>">Your QDiscuss Now!</a></p>
 	
 	<h3>General Forum Setting</h3>
 	<hr>
  	<table class="form-table">
  		<tr>
  			<th><?php _e('Key'); ?></th>
  			<th><?php _e('Value'); ?></th>
  			<th><?php _e('Apply'); ?></th>
  		</tr>
  		<?php foreach ($settings as $setting) :?>
 	 	<tr>
 		 	<td><?php echo $setting['key']; ?></td>
 		              <td><?php echo $setting['value']; ?></td>
 		              <td><a href="<?php echo admin_url() . 'admin.php?page=qdiscuss-config-settings&key=' . $setting['key']; ?>"><?php _e('Edit'); ?></a></td>
 		</tr>
 	 	<?php endforeach; ?>
  	</table>

 	<p></p>
 	
 	<h3>Forum Users Groups Setting</h3>
 	<hr>

 	<p><a href="<?php echo admin_url('admin.php?page=qdiscuss-users'); ?>">Manage</a> your user group in QDiscuss.</p>

 	<p></p>

 	<h3>See Our Other Awesomeness</h3>
 	<hr>
 	<div>
 		<a href="http://colorvila.com/qdiscuss" target="blank"><img alt="qdiscuss forum" src="<?php echo QDISCUSS_URI .  '/public/dashboard/images/support-forum.jpg'; ?>"/></a>
 		<a href="http://colorvila.com/themes" target="blank"><img alt="qdiscuss themes" src="<?php echo QDISCUSS_URI .  '/public/dashboard/images/themes-club.jpg'; ?>"/></a>
 	</div>

</div>