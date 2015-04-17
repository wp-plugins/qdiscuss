 <div class="wrap">
 	<h2>QDiscuss Settings</h2>
 		
 	<table class="form-table">


 		<table class="wp-list-table widefat fixed users">
		<thead>
			<tr>
				<th scope="col" id="userid" class="manage-column column-userid sortable desc column-posts" style="">
					<a href="#">
						<span><?php _e('ID'); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" id="userlogin" class="manage-column column-userlogin sortable desc" style="">
					<a href="#">
						<span><?php _e('Name'); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				</th>
				<th scope="col" id="qdiscuss-group" class="manage-column column-qdiscuss-group sortable desc" style="">
					<a href="#">
						<span><?php _e('QDiscuss User Group'); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" id="edit" class="manage-column column-edit sortable desc" style="">
					<a href="#">
						<span><?php _e('Edit'); ?></span>		
					</a>			
				</th>
			</tr>
		</thead>

		
		<tfoot>
			<div class="tablenav-pages" style="margin-bottom:10px;"><span class="displaying-num"><?php echo $total ; ?> <?php  _e('items' , 'qdiscuss'); ?></span>
				<?php echo $pagination; ?>
			</div>
		</tfoot>
		

		<tbody id="the-list" data-wp-lists="list:user">
			<?php foreach ($wp_users as $user) :?>
			 	<tr>
				 	<td><?php echo $user['ID']; ?></td>
				             <td><?php echo $user['user_login']; ?></td>
				             <td><?php echo $user['group']; ?></td>
				             <td><a href="<?php echo admin_url() . 'admin.php?page=qdiscuss-roles-settings&id=' . $user['user_id'] . '&wp_user_id=' . $user['ID']; ?>"><?php _e('Edit'); ?></a></td>
			 	</tr>
		 	<?php endforeach; ?>
		</tbody>


	</table>


</div>