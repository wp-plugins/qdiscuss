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
				window.location = params.config_settings_redirect;
		       
			} else if(response==2){
				alert('Invalid input, please check and try again!');            
				success.html('Save');    
			}else {
		         		success.html('Save');    
		   	}
		});

	});
});
   