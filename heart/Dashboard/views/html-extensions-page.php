<script>
	var installedExtensions = <?php echo json_encode(array_values($extensions)); ?>;
	var qd_manager_server = 'http://colorvila.com/extension-manager';
	var qd_manager_endpoint = 'extension-manager';
</script>
<?php include('html-header.php'); ?>

	<div id='qd-extensions' class="wrap  qd-wrap">
		<ul class="subsubsub qd-header qd-fixed">
			<li class="installed-list qd-active"><a  v-on="click: onActive('.online-list', $event)"  class="small">Installed Extensions</a></li>
			<li class="online-list"><a  v-on="click: onActive('.installed-list', $event)" class="small" class="online-list">Browser All Extensions</a></li>
		</ul>
		<hr>
		<?php if($extensions) :?>
		<div id="qd-spinning"></div>
	 	<table class="form-table installed-extensions-table">
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
			         		<a v-on="click: onClick(status, $event)" href="#"  data-id={{name}} data-setting-data="setting_method={{ (status == 1) ? 'deactivate' : '' }}{{ (status == 0 || !status) ? 'activate' : '' }}{{ (status == 3) ? 'update' : '' }}&extension_name={{name}}&download_url={{download_url}}"  class="save-extensions-setting">
		         				{{ (status == 1) ? 'Deactivate' : '' }}
		         				{{ ((status == 0 || !status) && (version > '0.0.3')) ? 'Activate' : '' }}
		         				{{ (version <= '0.0.3' || status == 3) ? 'Update' : '' }}
			         		</a>
			         		&nbsp;&nbsp;
			         		<a v-on="click: onClickRemove($event)" href="#" data-id={{name}} data-setting-data="setting_method=remove&extension_name={{name}}" class="save-extensions-setting">
			         			{{ (status == 0 || !status || status == 3) ? 'Remove' : '' }}
			         		</a>
			         	</td>
			</tr>
	 	</table>
		<?php else :?>
			<p>No extension installed yet! Click 'the Browser All Extensions' tab above or just Browser <a target='blank' href="http://colorvila.com/qdiscuss-extensions/">All our Extensions</a> for QDiscuss now!</p>
		<?php endif;?>
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
	<script>
		var spinning = document.getElementById('qd-spinning');
		if (spinning) {
			var spinner = new Spinner({top: '20em' , left: '50%'}).spin(spinning);
		}
	</script>
