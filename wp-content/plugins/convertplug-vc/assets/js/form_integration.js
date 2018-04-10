(function( $ ) {

	function isValidEmailAddress(emailAddress) {
	    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
	    return pattern.test(emailAddress);
	};

	function validate_it( current_ele, value ) {
		if( !value.trim() ) {
			return true;
		} else if( current_ele.hasClass('cpvc-email') ) {
			if( !isValidEmailAddress( value ) ) {
				return true;
			}
			else {
				return false;
			}
		} else if( current_ele.hasClass('cp-textfeild') ) {
			if( /^[a-zA-Z0-9- ]*$/.test( value ) == false ) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}

	function cp_custom_form_process(t) {

		var form 			= jQuery(t).parents(".cpvc-custom-form-container").find("#cpvc-smile-optin-form");

		var number_of_email = form.find('.cpvc-email');
		if( number_of_email.length > 1 ) {

			number_of_email.each(function( index, el ){

				if( index != 0 ) {
					jQuery(this).attr('name','param[email_'+ index +']');
				}
				console.log(jQuery(this).attr('name'));
			})
		}

		var number_of_blank = form.find('.cpvc-input:not(.cpvc-email,.cpvc-submit-button)');
		if( number_of_blank.length > 0 ) {

			number_of_blank.each(function( index, el ){
				if( jQuery(this).attr('name') == "param[_BLANK_NAME]" ) {
					jQuery(this).attr('name','param[CP_FIELD_'+ index +']')
				}
			})
		}

		var	data 			= form.serialize(),
			info_container  = jQuery(t).parents(".cpvc-custom-form-container").find('.cpvc-addon-msg-on-submit'),
			form_container  = jQuery(t).parents(".cpvc-custom-form-container").find('.cpvc-custom-form-content'),
			spinner  		= jQuery(t).parents(".cpvc-custom-form-container").find('.cpvc-addon-form-processing'),
			cp_form_processing_wrap = jQuery(t).parents(".cpvc-custom-form-container").find('.cpvc-addon-form-processing-wrap'),
			cp_animate_container    = jQuery(t).parents(".cpvc-custom-form-container");

		var redirectdata 	= jQuery(t).parents(".cpvc-custom-form-container").data("redirect-lead-data");

		/* Check for required fields & Query String */
		var query_string ='';
		// var input_status = [];
		var submit_status = true;
		form.find('.cpvc-input').each( function(index) {
			var $this = jQuery(this);

			if( ! $this.hasClass('cpvc-submit-button')) { // Check condition for Submit Button
				var	input_name = $this.attr('name'),
					input_value = $this.val();

				var res = input_name.replace(/param/gi, function myFunction(x){return ''; });
				res = res.replace('[','');
				res = res.replace(']','');

				query_string += ( index != 0 ) ? "&" : '';
				query_string += res+"="+input_value ;

				var input_required = $this.attr('required') ? true : false;

				if( input_required ) {
					if( validate_it( $this, input_value ) ) {
						submit_status = false;
						$this.addClass('cpvc-input-error');
						// input_status.push([input_name,false]);
					} else {
						$this.removeClass('cpvc-input-error');
						// input_status.push([input_name,true]);
					}
				}
			}
		});
		console.log(query_string);


		if( submit_status ) {

			form_container.css({'visibility' : "hidden"});
			cp_form_processing_wrap.show();

			info_container.fadeOut(120, function() {
			    jQuery(this).show().css({visibility: "hidden"});
			});

			// Show processing spinner
			spinner.hide().css({visibility: "visible"}).fadeIn(100);

			redirect_new_tab = false;
			//console.log(form_container.data('redirect-target').trim());
			if( form_container.data('redirect-target').trim() == '_blank' ) {
				redirect_new_tab = true;
			}

			jQuery.ajax({
				url: cpvc_ajax.url,
				data: data,
				type: 'POST',
				dataType: 'HTML',
				success: function(result){

					console.log(result);

					var obj = jQuery.parseJSON( result.replace('/','') );
					var cls = '';

					if( typeof obj.status != 'undefined' && obj.status != null ) {
						cls = obj.status;
					}

					//	is valid - Email MX Record
					/*if( obj.email_status ) {
						form.find('.cp-addon-email').removeClass('cp-addon-error');
					} else {
						form.find('.cp-addon-email').addClass('cp-addon-error');
						form.find('.cp-addon-email').focus();
					}*/

					//	show message error/success

					if( typeof obj.message != 'undefined' && obj.message != null ) {
						info_container.hide().css({visibility: "visible"}).fadeIn(120);
						info_container.html( '<div class="cpvc-m-'+cls+'">'+obj.message+'</div>' );
						cp_animate_container.addClass('cpvc-addon-form-submit-'+cls);
					}

					if(typeof obj.action !== 'undefined' && obj.action != null){

						//	Show processing spinner
						spinner.fadeOut(100, function() {
						    jQuery(this).show().css({visibility: "hidden"});
						});

						//	Hide error/success message
						info_container.hide().css({visibility: "visible"}).fadeIn(120);

						if( cls === 'success' ) {

							//hide tool tip
							//jQuery('head').append('<style class="cp-tooltip-css">.tip.'+cp_tooltip+'{display:none }</style>');

							// 	Redirect if status is [success]
							if( obj.action === 'redirect' ) {
								cp_form_processing_wrap.show();
								/*slidein.hide();*/
								var url =obj.url;

								var urlstring ='';
								if (url.indexOf("?") > -1) {
								    urlstring = '&';
								} else {
									urlstring = '?';
								}

								var redirect_url = url+urlstring+decodeURI(query_string);
								if( redirectdata == 1 ){
									if( redirect_new_tab ) {
										window.open(redirect_url,'_blank');
									} else {
										window.location = redirect_url;
									}
								} else {
									if( redirect_new_tab ) {
										window.open(obj.url,'_blank');
									} else {
										window.location = obj.url;
									}
								}
							} else {
								cp_form_processing_wrap.show();
							}

							/*if(dont_close){
								setTimeout(function(){
						           jQuery(document).trigger('closeSlideIn',[slidein]);

						         },3000);
							}*/
						} else {
							//form_container.show();
							jQuery('.cpvc-addon-form-submit-error').find('.cpvc-m-error').click(function(e){
								form_container.css({'visibility' : "visible"});
								cp_form_processing_wrap.hide();
								cp_animate_container.removeClass('cpvc-addon-form-submit-error');
							});
						}
					}

				},
				error: function(data){
					//	Show form & Hide processing spinner
					cp_form_processing_wrap.hide();
					spinner.fadeOut(100, function() {
					    jQuery(this).show().css({visibility: "hidden"});
					});
		        }
			});
		}
	}

	jQuery(document).ready(function(){

		jQuery('.cpvc-custom-form-container #cpvc-smile-optin-form').each(function(index, el) {

			// enter key press
			jQuery(el).find("input").keypress(function(event) {
			    if ( event.which == 13 ) {
			        event.preventDefault();
			        var check_sucess = jQuery(this).parents(".cpvc-custom-form-container").hasClass('cpvc-addon-form-submit-success');
			        var check_error = jQuery(this).parents(".cpvc-custom-form-container").hasClass('cpvc-addon-form-submit-error');

			        if(!check_sucess){
			        	cp_custom_form_process(this);
			    	}
			    }
			});

		    // submit add subscriber request
		    jQuery('.cpvc-custom-form-container').find('button.btn-subscribe').click(function(e){
				e.preventDefault;
				cp_custom_form_process(this);
			});
		});

		/* Apply Custom CSS of Inputs*/
		jQuery('.cpvc-custom-form-container').each(function(index,el) {
			var self = jQuery(this),
				custom_class = self.data('custom_class'),
				styles = self.data('custom_styles'),
				shadow_color = self.data('shadow_color');
				text_color = self.data('text_color');
				label_text_color = self.data('label_text_color');
				label_font_size = self.data('label_font_size');
				input_vertical_pedding = self.data('input_vertical_pedding');
				input_horizontal_pedding = self.data('input_horizontal_pedding');
				input_font_size = self.data('input_font_size');


			var str = "<style> ." + custom_class + " .cpvc-form-field .cpvc-input { " + styles + "}";
			str += " ." + custom_class + ".cpvc-style_1 .cpvc-input.not(.cpvc-submit-button):focus { box-shadow: 0 0 4px " + shadow_color + "; } ";
			str += " ." + custom_class + ".cpvc-style_2 .cpvc-input, ." + custom_class + ".cpvc-style_3	 .cpvc-input { box-shadow: 0 1px 0 " + shadow_color + "; } ";
			str += " ." + custom_class + ".cpvc-style_2 .cpvc-input::not(.cpvc-submit-button):focus, ." + custom_class + ".cpvc-style_3	 .cpvc-input:not(.cpvc-submit-button):focus { box-shadow: 0 1px 0 " + shadow_color + "; border:none; } ";
			str += " ." + custom_class + " .cpvc-form-field textarea.cpvc-input::-webkit-input-placeholder, ." + custom_class + "  .cpvc-form-field input.cpvc-input::-webkit-input-placeholder { color: "+ text_color +" !important; }";
			str += " ." + custom_class + " .cpvc-form-field textarea.cpvc-input:-moz-placeholder, ." + custom_class + "  .cpvc-form-field input.cpvc-input:-moz-placeholder { color: "+ text_color +" !important; }";
			str += " ." + custom_class + " .cpvc-form-field textarea.cpvc-input::-moz-placeholder, ." + custom_class + "  .cpvc-form-field input.cpvc-input::-moz-placeholder {  color: "+ text_color +" !important;  }";
			str += " ." + custom_class + " .cpvc-form-field textarea.cpvc-input:-ms-input-placeholder, ." + custom_class + "  .cpvc-form-field input.cpvc-input:-ms-input-placeholder {  color: "+ text_color +" !important;  }";
			str += " ." + custom_class + ".cpvc-custom-form-container.cpvc-style_3 .cpvc-form-field label { top :"+ input_vertical_pedding + "px; left:"+ input_horizontal_pedding +"px}";
			str += " ." + custom_class + " .cpvc-form-field > label { color: "+ label_text_color +"; font-size:"+ label_font_size +"px;}";
			str += " ." + custom_class + ".cpvc-custom-form-container.cpvc-style_3 .cpvc-form-field > label { font-size:"+ input_font_size +"px;}";
			str += " ." + custom_class + ".cpvc-custom-form-container.cpvc-style_3 .cpvc-form-field > label.focus_in_label { font-size:"+ label_font_size +"px;}";
			str += "</style>"
			jQuery('head').append(str);
		});

		jQuery('.cpvc-custom-form-container.cpvc-style_3').each(function(index,el) {
			jQuery('.cpvc-input:not(.cpvc-submit-button)').on('focus',function(){
				var label = jQuery(this).closest('.cpvc-form-field').find('label');
				label.addClass('focus_in_label');
			});

			jQuery('.cpvc-input:not(.cpvc-submit-button)').on('blur',function() {
				var label = jQuery(this).closest('.cpvc-form-field').find('label');
				var str = jQuery(this).val().trim();
				if( str == "" ) {
					label.removeClass('focus_in_label');
				}
			});
		});

	});

})( jQuery );
