(function( $ ) {
	
	jQuery(".cpvc-name-param").each(function(index, element) {
    	
      	jQuery(this).find('.cpvc-name-field').on('blur', function() {
      		var str = jQuery(this).val();
      		str = str.replace(/([~!@#$%^&*()+=`{}\[\]\|\\:;"'<>,.\/\-? ])+/g,'').replace(/^(-)+|(-)+$/g,'');
      		jQuery(this).val(str);
      	})
  	});

	//cpvc_select_campaign
	jQuery(document).ready(function() {
  
    jQuery.ajax({
      url: ajaxurl,
      data: {  
        action: 'cp_get_active_campaigns', 
        source: 'cp-addon' 
      },
      method: "POST",
      dataType: "JSON",
      success: function( result ){ 
        var oldSelected = jQuery('.cpvc_select_campaign.dropdown').find('option[selected="selected"]').text();
        var options = '<option class="no-list" value="no-list" selected="selected">-- Select Campaign --</option>';
        console.log(result);

        jQuery.each(result, function( key, value ) {
          if( value["list-name"] == oldSelected ) {
            options += '<option class="'+ key +'" value="'+ key +'" selected="selected">' + value["list-name"] + '</option>';
          } else {
            options += '<option class="'+ key +'" value="'+ key +'">' + value["list-name"] + '</option>';
          }
        });

        jQuery('.cpvc_select_campaign.dropdown').html( options );
      },
      error: function() {
        console.log("Error: Get Connect's List")
      }
    });

	});
  	

})( jQuery );