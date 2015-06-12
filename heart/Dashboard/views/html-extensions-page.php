<script>
	var installedExtensions = <?php echo json_encode(array_values($extensions)); ?>;
	var qd_manager_server = 'http://colorvila.com/extension-manager';
	var qd_manager_endpoint = 'extension-manager';
</script>
<?php include('html-header.php'); ?>

	<div id='qd-extensions' class="wrap  qd-wrap">
		<ul class="subsubsub qd-header qd-fixed">
			<li v-on="click: onActive('.online-list', $event)" class="installed-list qd-active"><a class="small">Installed Extensions</a></li>
			<li v-on="click: onActive('.installed-list', $event)" class="online-list"><a class="small" class="online-list">Browser All Extensions</a></li>
		</ul>
		<hr>
	 	<table class="form-table installed-extensions-table">
	 	<?php if($extensions) :?>
	 		<tr>
	 			<th><?php _e('Name'); ?></th>
	 			<th><?php _e('Description'); ?></th>
	 			<th><?php _e('Version'); ?></th>
	 			<th><?php _e('Apply'); ?></th>
	 		</tr>
		 	<tr v-repeat="showExtensions">
			 	<td>{{name}}</td>
			         	<td>{{description}}</td>
			         	<td v-model="version">{{version}}</td>
			         	<td>
			         		<a v-on="click: onClick" href="#" data-setting-data="setting_method={{ (status == 1) ? 'deactivate' : '' }}{{ (status == 0 || !status) ? 'activate' : '' }}{{ (status == 3) ? 'update' : '' }}&extension_name={{name}}&download_url={{download_url}}"  class="save-extensions-setting">
		         				{{ (status == 1) ? 'Deactivate' : '' }}
		         				{{ ((status == 0 || !status) && (version > '0.0.3')) ? 'Activate' : '' }}
		         				{{ (version <= '0.0.3' || status == 3) ? 'Update' : '' }}
			         		</a>
			         	</td>
			</tr>
	 	<?php else :?>
	 		<p>No extension installed yet! Click 'the Browser All Extensions' tab above or just Browser <a target='blank' href="http://colorvila.com/qdiscuss-extensions/">All our Extensions</a> for QDiscuss now!</p>
	 	<?php endif;?>
	 	</table>
		
		<p></p>
		<p></p>
		<div id="online-extensions" class="online-extensions-table" style="display: none;">
			<div class="plugin-card"  v-repeat="onlineExtensions">
				<div class="plugin-card-top">
					<a href="http://colorvila.com/qdiscuss-extensions" class="thickbox plugin-icon"><img src="{{logo_url}}"></a>
					<div class="name column-name">
						<h4><a href="http://colorvila.com/qdiscuss-extensions" class="thickbox">{{name}}</a></h4>
					</div>
					<div class="action-links">
						<ul class="plugin-action-buttons">
							<li>
								<a v-on="click: onClickInstall(status, $event)" href="#" data-id={{id}} data-setting-data="setting_method={{ (status == 0 || !status) ? 'install' : '' }}&extension_name={{name}}&download_url={{download_url}}"  class="save-extensions-setting install-now button">
									{{(status == 1) ? 'Installed' : ''}}
									{{(status == 0 || !status) ? 'Install Now' : ''}}
								</a>
							</li>
						</ul>				
					</div>
					<div class="desc column-description">
						<p>{{description}}</p>
						<p class="authors">Created <cite>by <a href="http://colorvila.com">ColorVila</a></cite></p>
					</div>
				</div>
				<div class="plugin-card-bottom">
					<p>Version: {{latest_version}}    Downloads: {{download_counts ? download_counts : 0}}</p>
				</div>
			</div>
		</div>
	 	
		<?php// include('html-footer.php'); ?>
	</div>
