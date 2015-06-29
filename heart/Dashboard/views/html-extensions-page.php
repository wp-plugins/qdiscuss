<script>
	var installedExtensions = <?php echo json_encode(array_values($extensions)); ?>;
	var qd_manager_server = 'http://colorvila.com/extension-manager';
	var qd_manager_endpoint = 'extension-manager';
	var lanRemove = '<?php echo _e('Remove'); ?>';
	var lanRemoving = lanRemove;
	var lanActivate = '<?php echo _e('Activate'); ?>';
	var lanActivating = '<?php echo _e('Activate'); ?>';
	var lanDeactivate = '<?php echo _e('Deactivate'); ?>';
	var lanDeactivating = '<?php echo _e('Deactivate'); ?>';
	var lanUpdate = '<?php echo _e('Update'); ?>';
	var lanUpdating = '<?php echo _e('Update'); ?>';
	var lanSave = '<?php echo _e('Save'); ?>';
	var lanSaving = '<?php echo _e('Save'); ?>';
	var lanInstall  =  '<?php echo _e('Install'); ?>';
	var lanInstalled = '<?php echo _e('Installed'); ?>';
	var lanInstalling   =  '<?php echo _e('Install'); ?>';
</script>
	<div id='qd-extensions' class="">
		<h2><?php echo _e("Extensions"); ?></h2>
		<hr>
		<ul class="subsubsub qd-header qd-fixed">
			<li class="installed-list qd-active"><a  v-on="click: onActive('.online-list', $event)"  class="small"><?php echo _e("Installed Extensions"); ?></a></li>
			<li class="online-list"><a  v-on="click: onActive('.installed-list', $event)" class="small" class="online-list"><?php echo _e("Browser All Extensions");?></a></li>
		</ul>
		<div class="container"></div>
		<?php if($extensions) :?>
		<div id="qd-spinning"></div>
		 	<table class=" table table-hover installed-extensions-table">
		 		<tr>
		 			<th><?php _e('Title'); ?></th>
		 			<th><?php _e('Description'); ?></th>
		 			<th><?php _e('Version'); ?></th>
		 			<th><?php _e('Apply'); ?></th>
		 			<th></th>
		 			<th></th>
		 		</tr>
			 	<tr v-repeat="showExtensions">
				 	<td>{{name}}</td>
				         	<td>{{description}}</td>
				         	<td v-model="version">{{version}}</td>
				         	<td>
				         		<template v-if="(status == 0 || !status || status == 3)">
				         			<a v-on="click: onClickRemove($event)" data-id={{name}} data-setting-data="setting_method=remove&extension_name={{name}}" class="btn btn-danger save-extensions-setting">
							{{ lanRemove }}
							</a>
				         		</template>
				         	<td>
				         	<td>
		         		         		<template v-if="(status == 1)">
		         		         			<a v-on="click: onClick(status, $event)" data-id={{name}} data-setting-data="setting_method=deactivate&extension_name={{name}}" class="btn btn-primary save-extensions-setting">
		         					{{ lanDeactivate }}
		         					</a>
		         		         		</template>
         		         		         		<template v-if="((status == 0 || !status) && (version > '0.0.3'))">
         		         		         			<a v-on="click: onClick(status, $event)" data-id={{name}} data-setting-data="setting_method=activate&extension_name={{name}}" class="btn btn-primary save-extensions-setting">
         		         					{{ lanActivate }}
         		         					</a>
         		         		         		</template>
		         		         		<template v-if="status == 3">
		         		         		      	<a v-on="click: onClick(status, $event)" data-id={{name}} data-setting-data="setting_method=update&extension_name={{name}}&download_url={{download_url}}" class="btn btn-primary save-extensions-setting">
		         		         				{{ lanUpdate }}
		         		         			</a>
		         		         		</template>
				         	</td>
				</tr>
		 	</table>
		<?php else :?>
			<p>No extension installed yet! Click 'the Browser All Extensions' tab above or just Browser <a target='blank' href="http://colorvila.com/qdiscuss-extensions/">All our Extensions</a> for QDiscuss now!</p>
		<?php endif;?>
		<p></p>
		<p></p>
		<div id="online-extensions" class="online-extensions-table" style="display: none;">
			<div class="plugin-card well"  v-repeat="onlineExtensions">
				<div class="plugin-card-top">
					<a href="http://colorvila.com/qdiscuss-extensions" class="thickbox plugin-icon"><img src="{{logo_url}}"></a>
					<div class="name column-name">
						<h4><a href="http://colorvila.com/qdiscuss-extensions" class="thickbox">{{name}}</a></h4>
					</div>
					<div class="action-links">
						<ul class="plugin-action-buttons">
							<li>
								<template v-if="status == 1">
									<a v-on="click: onClickInstall(status, $event)" data-id={{id}} data-setting-data=""  class="save-extensions-setting install-now button">
										{{ lanInstalled }}
									</a>
								</template>
								<template v-if="status == 0 || !status">
									<a v-on="click: onClickInstall(status, $event)" data-id={{id}} data-setting-data="setting_method=install&extension_name={{name}}&download_url={{download_url}}"  class="save-extensions-setting install-now button">
										{{ lanInstall }}
									</a>	
								</template>
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
	</div>
	<script>
		var spinning = document.getElementById('qd-spinning');
		if (spinning) {
			var spinner = new Spinner({top: '20em' , left: '50%'}).spin(spinning);
		}
	</script>
