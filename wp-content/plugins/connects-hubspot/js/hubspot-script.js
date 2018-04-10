// JavaScript Document
jQuery(document).on("change keyup paste keydown","#hubspot_api_key", function(e) {
	var val = jQuery(this).val();
	if( val !== "" )
		jQuery("#auth-hubspot").removeAttr('disabled');
	else
		jQuery("#auth-hubspot").attr('disabled','true');
});

// Hubspot authentication
jQuery(document).on( "click", "#auth-hubspot", function(e){

	e.preventDefault();
	jQuery(".smile-absolute-loader").css('visibility','visible');
	var api_key = jQuery("#hubspot_api_key").val();
	var action = 'update_hubspot_authentication';
	var data = {action:action,api_key:api_key};
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		type: 'POST',
		dataType: 'JSON',
		success: function(result){
			if(result.status == "success" ){
				jQuery(".bsf-cnlist-mailer-help").hide();
				jQuery("#save-btn").removeAttr('disabled');
				jQuery("#hubspot_api_key").closest('.bsf-cnlist-form-row').hide();
				jQuery("#auth-hubspot").closest('.bsf-cnlist-form-row').hide();
				jQuery(".hubspot-list").html(result.message);
			} else {
				jQuery(".hubspot-list").html('<span class="bsf-mailer-error">'+result.message+'</span>');
			}
			jQuery(".smile-absolute-loader").css('visibility','hidden');
		}
	});
	e.preventDefault();
});

jQuery(document).on( "click", "#disconnect-hubspot", function(){
															
	if(confirm("Are you sure? If you disconnect, your previous campaigns syncing with hubspot will be disconnected as well.")) {
		var action = 'disconnect_hubspot';
		var data = {action:action};
		jQuery(".smile-absolute-loader").css('visibility','visible');
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'JSON',
			success: function(result){

				jQuery("#save-btn").attr('disabled','true');
				if(result.message == "disconnected" ){

					jQuery("#hubspot_api_key").val('');
					jQuery(".hubspot-list").html('');
					jQuery("#disconnect-hubspot").replaceWith('<button id="auth-hubspot" class="button button-secondary auth-button" disabled="true">Authenticate HubSpot</button><span class="spinner" style="float: none;"></span>');
					jQuery("#auth-hubspot").attr('disabled','true');
				}

				jQuery('.bsf-cnlist-form-row').fadeIn('300');
				jQuery(".bsf-cnlist-mailer-help").show();
				jQuery(".smile-absolute-loader").css('visibility','hidden');
			}
		});
	}
	else {
		return false;
	}
});