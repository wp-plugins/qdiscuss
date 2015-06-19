var qd_manager_server = qd_manager_server ? qd_manager_server : 'http://colorvila.com/extension-manager';
var installedExtensions = installedExtensions ? installedExtensions : {};
jQuery(function ($) {

	$('#save-config-setting').click(function() {
		var serializedReturn = $('#qdiscuss-settings-form :input[name][name!="security"][name!="of_reset"]').serialize();
		var data = {
			type: 'save',
			action: 'qdiscuss_ajax_config_settings_save',
			//  security: nonce,
			data: serializedReturn
		};

		var success = $('#save-config-setting');
		success.html('Saving ...');

		$.post(ajaxurl, data, function(response) {

			if (response==1) {
				window.location = qdiscuss_admin_params.config_settings_redirect;
			} else if(response==2){
				alert('Invalid input, please check and try again!');            
				success.html('Save');    
			}else {
		         		success.html('Save');    
		   	}
		});

	});
});

jQuery(function ($) {
	$('#save-roles-config-setting').click(function() {
		var serializedReturn = $('#qdiscuss-roles-settings-form :input[name][name!="security"][name!="of_reset"]').serialize();
		var data = {
			type: 'save',
			action: 'qdiscuss_ajax_roles_settings_save',
			//  security: nonce,
			data: serializedReturn
		};

		var success = $('#save-roles-config-setting');
		success.html('Saving ...');

		$.post(ajaxurl, data, function(response) {

			if (response==1) {
				window.location = qdiscuss_admin_params.roles_settings_redirect;
			} else if(response==2){
				alert('Invalid input, please check and try again!');            
				success.html('Save');    
			}else {
		         		success.html('Save');    
		   	}
		});

	});
});

function InstantExtentions(callback) {
	var request = function() {
		jQuery(function($){
			$.get(qd_manager_server + '/get', function(response){
				$('#qd-spinning').remove();
				$('.installed-extensions-table').show();
				callback(response);
			});
		});
	};
	request();
	setInterval(request, 300000);
}

InstantExtentions(function (result) {

	var ExtensionsBox = new Vue({
		el: '#qd-extensions',
		data: {
			installedExtensions: installedExtensions,
			instantExtensions: JSON.parse(result)
		},
		computed: {
			showExtensions: {
				get: function() {
					extensions = this.installedExtensions;
					for (e in extensions) {
						for (i in this.instantExtensions) {
							if (extensions[e].name == this.instantExtensions[i].name) {
								if (extensions[e].version < this.instantExtensions[i].latest_version) {
									extensions[e].status = 3;
									extensions[e].download_url = this.instantExtensions[i].download_url;
								} else {
									extensions[e].download_url = '';
								}
							} 
						}
					}
					return extensions;
				}
			},
			onlineExtensions: {
				get: function() {
					instantExtensions = this.instantExtensions;
					for (i in instantExtensions) {
						for (e in this.installedExtensions) {
							if (instantExtensions[i].name == this.installedExtensions[e].name) {
								instantExtensions[i].status = 1;// installed
							}
						}
					}

					return instantExtensions;
				}
			}
		},
		methods: {
			onClick: function(status, e){
				jQuery(function ($) {
					var data = {
						type: 'post',
						action: 'qdiscuss_ajax_extensions_settings_save',
						//  security: nonce,
						data: $(e.target).data('setting-data')
					};

					$(e.target).html('Saving ...');
					
					if (status == 3) {
						$.post(qd_manager_server + '/count/' + $(e.target).data('id'), function(response){
							console.log(response);
						});
					}

					$.post(ajaxurl, data, function(response) {
						if (response==1) {
							window.location = qdiscuss_admin_params.extensions_settings_redirect;
						} else if(response==2){
							alert('Invalid input, please check and try again!');            
							window.location = qdiscuss_admin_params.extensions_settings_redirect;
						} else {
					        	//	alert('Error, please Upgrade the extension MANUALLY!');            
							window.location = qdiscuss_admin_params.extensions_settings_redirect;
					   	}
					});
				}); 
			},
			onActive: function(className, e) {
				jQuery(function($){
					$(className).removeClass('qd-active');
					$(e.target).parent('li').addClass('qd-active');
					if (className == '.installed-list') {
						$('table.installed-extensions-table').hide();
						$('.online-extensions-table').show();	
					} else {
						$('.online-extensions-table').hide();
						$('table.installed-extensions-table').show();
					}
				});
			},
			onClickInstall: function(status, e){
				if (status) {
					return false;
				} else {
					jQuery(function ($) {
						var data = {
							type: 'post',
							action: 'qdiscuss_ajax_extensions_settings_save',
							//  security: nonce,
							data: $(e.target).data('setting-data')
						};

						$(e.target).html('Installing ...');

						$.post(qd_manager_server + '/count/' + $(e.target).data('id'), function(response){
							console.log(response);
						});

						$.post(ajaxurl, data, function(response) {
							if (response==1) {
								window.location = qdiscuss_admin_params.extensions_settings_redirect;
							} else if(response==2){
								alert('Invalid input, please check and try again!');            
								window.location = qdiscuss_admin_params.extensions_settings_redirect;
							} else {
						        	//	alert('Error, please Upgrade the extension MANUALLY!');            
								window.location = qdiscuss_admin_params.extensions_settings_redirect;
						   	}
						});
					}); 
				}
			},
			onClickRemove: function(e) {
				if (confirm('Warning:The remove action will delete all the datas related this extension, make sure you have backed up yours datas!')) {
					jQuery(function ($) {
						var data = {
							type: 'post',
							action: 'qdiscuss_ajax_extensions_settings_save',
							//  security: nonce,
							data: $(e.target).data('setting-data')
						};

						$(e.target).html('Moving ...');

						$.post(ajaxurl, data, function(response) {
							if (response==1) {
								window.location = qdiscuss_admin_params.extensions_settings_redirect;
							} else if(response==2){
								alert('Invalid input, please check and try again!');            
								window.location = qdiscuss_admin_params.extensions_settings_redirect;
							} else {
						        		// alert('Error, please Upgrade the extension MANUALLY!');            
								window.location = qdiscuss_admin_params.extensions_settings_redirect;
						   	}
						});
					}); 
				}
			}
		}
	});

});
   
