<?php
/*
Plugin Name: Convert Plus Addons for Visual Composer
Plugin URI: https://www.convertplug.com/plus
Author: Brainstorm Force
Author URI: https://www.brainstormforce.com
Version: 2.0.0
Description: Easy to use form builder for Visual Composer users. Now you can create form with unlimited fields, save form submission data in campaigns, see analytics, graphs & best of all - third party sync with Mailchimp, MyMail etc.
Text Domain: convertplug_vc
*/

if(!defined('__CONVERTPLUG_VC_ROOT__')){
	define('__CONVERTPLUG_VC_ROOT__', dirname(__FILE__));
}
if(!defined('CONVERTPLUG_VC_VERSION')){
	define('CONVERTPLUG_VC_VERSION', '1.0.1');
}

register_activation_hook( __FILE__, 'cpvc_plugin_activate');
function cpvc_plugin_activate()
{
	$memory = ini_get('memory_limit');
	$allowed_memory = preg_replace("/[^0-9]/","",$memory)*1024*1024;
	$peak_memory = memory_get_peak_usage(true);
	if($allowed_memory - $peak_memory <= 14436352){
		$pre = __('Unfortunately, plugin could not be activated. Not enough memory available.','convertplug_vc');
		$sub = __('Please contact', 'convertplug_vc');
		trigger_error( $pre.' '.$sub.' <a href="https://support.brainstormforce.com/">'.__('plugin support','convertplug_vc').'</a>.',E_USER_ERROR );
	}
}

if (! class_exists( "ConvertPlug_VC" )) {
	class ConvertPlug_VC {

		function __construct() {
			add_action('admin_init', array( $this, 'init_addons' ) );

			add_action('after_setup_theme',array($this,'param_inits'));

			/* Display Inline */
			// add_action('vc_after_init',array($this,'cpvc_inline_mapper'));
			// add_shortcode('cpvc_inline',array($this,'cpvc_inline_shortcode'));

			/* CP Custom Form Elements */
			add_action('vc_after_init',array($this,'cpvc_custom_form_element'));
			add_shortcode('cpvc_custom_form_container',array($this,'cpvc_custom_form_container_callback'));
			add_shortcode('cpvc_textfield',array($this,'cpvc_textfield_callback'));
			add_shortcode('cpvc_emailfield',array($this,'cpvc_emailfield_callback'));
			add_shortcode('cpvc_textarea',array($this,'cpvc_textarea_callback'));
			add_shortcode('cpvc_number',array($this,'cpvc_number_callback'));
			add_shortcode('cpvc_dropdown',array($this,'cpvc_dropdown_callback'));
			add_shortcode('cpvc_hidden_field',array($this,'cpvc_hidden_field_callback'));
			add_shortcode('cpvc_submit_button',array($this,'cpvc_submit_button_callback'));

			add_action('wp_enqueue_scripts',array($this,'enqueue_front_scripts'));
			add_action('admin_enqueue_scripts',array($this,'enqueue_admin_scripts'));
		}

		function init_addons() {
			if( !defined('CP_VERSION')) {
				add_action( 'admin_notices', array( $this, 'admin_notice_for_cp_activation'));
					add_action('network_admin_notices', array( $this, 'admin_notice_for_cp_activation'));
			}

			$required_vc = '3.7';
			if( defined('WPB_VC_VERSION') ) {
				if( version_compare( $required_vc, WPB_VC_VERSION, '>' )){
					add_action( 'admin_notices', array( $this, 'admin_notice_for_version'));
					add_action('network_admin_notices', array( $this, 'admin_notice_for_version'));
				}
			} else {
				add_action( 'admin_notices', array( $this, 'admin_notice_for_vc_activation'));
				add_action('network_admin_notices', array( $this, 'admin_notice_for_vc_activation'));
			}
		}

		// Function to Notice for Install & Active Visual Composer
		function admin_notice_for_cp_activation() {
			$is_multisite = is_multisite();
			$is_network_admin = is_network_admin();
			if( ( $is_multisite && $is_network_admin ) || !$is_multisite )
				echo '<div class="updated"><p>'.__('The','convertplug_vc').' <strong>'.CP_PLUS_NAME.' Addon for Visual Composer</strong> '.__('plugin requires','convertplug_vc').' <strong>'.CP_PLUS_NAME.'</strong> '.__('Plugin installed and activated.','convertplug_vc').'</p></div>';
		}

		/* CP Name Param Initialization */
		function param_inits() {
	      if(defined('WPB_VC_VERSION') && version_compare(WPB_VC_VERSION, 4.8) >= 0) {
	        if(function_exists('vc_add_shortcode_param'))
	        {
	          vc_add_shortcode_param('cpvc_name', array($this, 'cpvc_name_callback'), plugins_url('/admin/js/cpvc_addon_admin.js',__FILE__));
	        }
	      }
	      else {
	        if(function_exists('add_shortcode_param'))
	        {
	          add_shortcode_param('cpvc_name', array($this, 'cpvc_name_callback'), plugins_url('/admin/js/cpvc_addon_admin.js',__FILE__));
	        }
	      }
	    }

	    /* CP Name Param Shortcode */
	    function cpvc_name_callback($settings, $value) {
	        $uid = 'cp-name'. rand(1000, 9999);
	        $dependency = '';
	        $html  = '<div class="cpvc-name-param" id="'.$uid.'" >';
	        $html  .= '<input type="text" name="'.$settings['param_name'].'" class="wpb_vc_param_value cpvc-name-field '.$settings['param_name'].' '.$settings['type'].'_field" value="'.$value.'" '.$dependency.' />';
	        $html  .='</div>';
	        return $html;
	    }

		/* Enqueue Front-end Script */
		function enqueue_front_scripts() {
			wp_register_style( 'cpvc-addon-for-vc-style', plugins_url('/assets/css/style.css',__FILE__) );
			wp_enqueue_style( 'cpvc-addon-for-vc-style' );

			wp_register_script( 'cpvc-addon-for-vc', plugins_url('/assets/js/form_integration.js',__FILE__), array( 'jquery' ),1.0,true);
			wp_localize_script( 'cpvc-addon-for-vc', 'cpvc_ajax', array( 'url' => admin_url( 'admin-ajax.php' ) ) );

			wp_enqueue_script( 'cpvc-addon-for-vc' );

			wp_register_style( 'cpvc-addon-for-vc-grid', plugins_url('/assets/css/cpvc-addon-grid.css',__FILE__) );
			wp_enqueue_style( 'cpvc-addon-for-vc-grid' );
		}

		/* Enqueue Admin Script */
		function enqueue_admin_scripts() {
			wp_register_style( 'cpvc-addon-icons', plugins_url('/admin/css/cpvc_addon_element_icons.css',__FILE__) );
			wp_enqueue_style( 'cpvc-addon-icons' );
		}

		// Function to Notice about Visual Composer Version
		function admin_notice_for_version() {
			$is_multisite = is_multisite();
			$is_network_admin = is_network_admin();
			if(($is_multisite && $is_network_admin) || !$is_multisite)
				echo '<div class="updated"><p>'.__('The','convertplug_vc').' <strong>ConvertPlug Addon for Visual Composer</strong> '.__('plugin requires','convertplug_vc').' <strong>Visual Composer</strong> '.__('version 3.7.2 or greater.','convertplug_vc').'</p></div>';
		}

		// Function to Notice for Install & Active Visual Composer
		function admin_notice_for_vc_activation() {
			$is_multisite = is_multisite();
			$is_network_admin = is_network_admin();
			if(($is_multisite && $is_network_admin) || !$is_multisite)
				echo '<div class="updated"><p>'.__('The','convertplug_vc').' <strong>'.CP_PLUS_NAME.' Addon for Visual Composer</strong> '.__('plugin requires','convertplug_vc').' <strong>Visual Composer</strong> '.__('Plugin installed and activated.','convertplug_vc').'</p></div>';
		}

		/* Get Module List Array */
		function get_module_styles_array( $Module_Styles ) {

			$CP_Styles = array();

			if ( is_array( $Module_Styles ) && count( $Module_Styles ) > 0 ) {

				$CP_Styles['-- Select Style --'] = '0';
				foreach ( $Module_Styles as $value ) {

					$Name = urldecode($value['style_name']);
					$Id = $value['style_id'];
					if( array_key_exists($Name,$CP_Styles) ) {

						$Name .= " - " . $Id . "";
					}
					$CP_Styles[$Name] = $Id;
				}
			} else {

				$CP_Styles['-- No Style Exist --'] = "0";
			}
			return $CP_Styles;
		}

		/* VC Mapper for CP Display Inline */
		function cpvc_inline_mapper(){
			// Fetch Active Modules
			$CP_Modules = get_option('convert_plug_modules');
			$CP_Modules_arr = array();
			foreach ($CP_Modules as $value) {
				$key = str_replace("_"," ",$value);
				$CP_Modules_arr[$key] = $value;
			}

			// Fetch Modal's All Styles
			$CP_Modal = get_option('smile_modal_styles');
			$CP_Modal_Styles = $this->get_module_styles_array( $CP_Modal );

			// Fetch Info Bar's All Styles
			$CP_Info_Bar = get_option('smile_info_bar_styles');
			$CP_Info_Bar_Styles = $this->get_module_styles_array( $CP_Info_Bar );

			// Fetch Slide In Modal's All Styles
			$CP_Slide_In = get_option('smile_slide_in_styles');
			$CP_Slide_In_Styles = $this->get_module_styles_array( $CP_Slide_In );

			if(function_exists('vc_map')) {
				vc_map(
					array(
						"name" => __("ConvertPlug Inline Display", "convertplug_vc"),
						"base" => "cpvc_inline",
						"icon" => "cpvc_display_inline_icon",
						"class" => "cpvc_display_inline_icon",
						"content_element" => true,
						"controls" => "full",
						"category" => "ConvertPlug VC Addon",
						"description" => __("Display Module's Style Inline",'convertplug_vc'),
						"params" => array(
							// add params same as with any other content element
							array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Module","convertplug_vc"),
								"param_name" => "cpvc_inline_module",
								"value" => $CP_Modules_arr,
								"description" => "This Version Supports only `Modal Popup Inline`.",
								"group" => "General",
								"admin_label" => true
						  	),
							array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Existing Modal Style","convertplug_vc"),
								"param_name" => "cpvc_modal_style",
								"value" => $CP_Modal_Styles,
								"description" => __("","convertplug_vc"),
								"group" => "General",
								"admin_label" => true,
								"dependency" => Array("element" => "cpvc_inline_module", "value" => "Modal_Popup" ),
						  	),
						  	array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Existing Info Bar Style","convertplug_vc"),
								"param_name" => "cpvc_info_bar_style",
								"value" => $CP_Info_Bar_Styles,
								"description" => __("","convertplug_vc"),
								"group" => "General",
								"admin_label" => true,
								"dependency" => Array("element" => "cpvc_inline_module", "value" => "Info_Bar" ),
						  	),
						  	array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Existing Slide In Style","convertplug_vc"),
								"param_name" => "cpvc_slide_in_style",
								"value" => $CP_Slide_In_Styles,
								"description" => __("","convertplug_vc"),
								"group" => "General",
								"admin_label" => true,
								"dependency" => Array("element" => "cpvc_inline_module", "value" => "Slide_In_Popup" ),
						  	),
						),
					)
				);
			}
		}

		/* Shortcode for CP Display Inline */
		function cpvc_inline_shortcode($atts){

			$output = $cpvc_inline_module = $cpvc_modal_style = $cpvc_info_bar_style = $cpvc_slide_in_style = '';

			extract(shortcode_atts(array(
				'cpvc_inline_module' => 'Modal_Popup',
				'cpvc_modal_style' => '',
				'cpvc_info_bar_style' => '',
				'cpvc_slide_in_style' => '',
			),$atts));

			if( $cpvc_inline_module == 'Modal_Popup' && trim($cpvc_modal_style) != "" ) {
				$output = do_shortcode("[cp_modal display='inline' id='" . $cpvc_modal_style . "'][/cp_modal]");

			} elseif( $cpvc_inline_module == 'Info_Bar' && trim($cpvc_info_bar_style) != "" ) {
				$output = do_shortcode("[cp_info_bar display='inline' id='" . $cpvc_info_bar_style . "'][/cp_info_bar]");

			} elseif( $cpvc_inline_module == 'Slide_In_Popup' && trim($cpvc_slide_in_style) != "" ) {
				$output = do_shortcode("[cp_slide_in display='inline' id='" . $cpvc_slide_in_style . "'][/cp_slide_in]");
			}

			return $output;
		}

		/* Get Dark Color */
		function get_dark_color( $cur_color ) {
			$dark_color = array();

			if( is_array($cur_color) ) {
				$dark_color['0'] = $cur_color['0'];
				$dark_color['1'] = $cur_color['1'];
				$dark_color['2'] = $cur_color['2'];
			} else {
				list($dark_color['0'], $dark_color['1'], $dark_color['2']) = sscanf($cur_color, "#%02x%02x%02x");
			}

			foreach ($dark_color as $key => $value) {
				if($value >= 25 ) {
					$dark_color[$key] = $value - 25;
				} else {
					$dark_color[$key] = 0;
				}
			}

			return implode (", ", array_reverse($dark_color));
		}

		/* Get Hidden Fields Elements */
		function cpvc_addon_get_form_hidden_fields( $a ){
			/** = Form options
			 *	Mailer - We will also optimize this by filter. If in any style we need the form then apply filter otherwise nope.
			 *-----------------------------------------------------------*/

			$mailer_id = $list_id = '';

		    $smile_lists = get_option('smile_lists');
		    $list = ( isset( $smile_lists[$a] ) ) ? $smile_lists[$a] : '';
		    $mailer = ( $list != '' ) ? $list['list-provider'] : '';
		    $listName = ( $list != '' ) ? str_replace(" ","_",strtolower( trim( $list['list-name'] ) ) ) : '';

		    if( $mailer == 'Convert Plug' ) {
		        $mailer_id = 'cp';
		        $list_id = $a;
		        $data_option = "cp_connects_".$listName;
		    } else {
		        $mailer_id = strtolower($mailer);
		        $list_id = ( $list != '' ) ? $list['list'] : '';
		        $data_option = "cp_".$mailer_id."_".$listName;
		    }
			ob_start();
			wp_nonce_field( 'cp-submit-form-' );
			$uid = time(); ?>

			<input type="hidden" name="param[user_id]" value="cp-uid-<?php echo $uid; ?>" />
	        <input type="hidden" name="param[date]" value="<?php echo esc_attr( date("j-n-Y") ); ?>" />
			<input type="hidden" name="list_parent_index" value="<?php echo isset( $a ) ? $a : ''; ?>" />
			<input type="hidden" name="action" value="<?php echo $mailer_id; ?>_add_subscriber" />
	        <input type="hidden" name="list_id" value="<?php echo $list_id; ?>" />
	        <input type="hidden" name="style_id" value="" />
	        <input type="hidden" name="option" value="<?php echo $data_option; ?>" />

	        <?php
	        $html = ob_get_clean();
	        return $html;
		}

		/* VC Mapper for CP Custom Form Elments */
		function cpvc_custom_form_element () {

			$smile_lists = get_option('smile_lists');
            $connects_list = array();
            $connects_list['-- Select Campaign --'] = 'no-list';
            if( count( $smile_lists ) > 0 ) {
                foreach( $smile_lists as $key=>$value ) {
                    $connects_list[ $value['list-name'] ] = $key;
                }
            }

			$connets_url = get_site_url()."/wp-admin/admin.php?page=contact-manager&view=new-list";

			if(function_exists('vc_map'))
			{
				vc_map(
					array(
						"name" => __("ConvertPlug Form Builder","convertplug_vc"),
						"base" => "cpvc_custom_form_container",
						"class" => "cpvc_custom_form_container_icon",
						"icon" => "cpvc_custom_form_container_icon",
						"category" => "ConvertPlug VC Addon",
						"description" => __("Design Custom Form","convertplug_vc"),
						"as_parent" => array('except' => 'cpvc_custom_form_container'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
						"content_element" => true,
						"show_settings_on_create" => true,
						//"is_container"    => true,
						"js_view" => 'VcColumnView',
						"params" => array(
							array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Select Campaign To Collect Leads","convertplug_vc"),
								"param_name" => "cpvc_select_campaign",
								"value" => $connects_list,
								"description" => "Dropdown will display the available campaign's list. If you would like, you can create a new campaign <a href='". $connets_url ."' target='_blank'>here</a>.",
								"group" => "Submission",
								"admin_label" => true
						  	),
						  	array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("After Successful Submission","convertplug_vc"),
								"param_name" => "cpvc_after_success",
								"value" => array(
										'Display a Message' => 'message',
										'Redirect User' => 'redirect'
									),
								"description" => __("","convertplug_vc"),
								"group" => "Submission",
						  	),
						  	array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Message After Success","convertplug_vc"),
								"param_name" => "cpvc_success_msg",
								"value" => "Thank you.",
								"description" => __("Enter the message you would like to display the user after successfully added to the list.","convertplug_vc"),
								"group" => "Submission",
								"dependency" => Array("element" => "cpvc_after_success", "value" => 'message'),
						  	),
						  	array(
								"type" => "vc_link",
								"class" => "",
								"heading" => __("Redirect URL","convertplug_vc"),
								"param_name" => "cpvc_redirect_url",
								"value" => "",
								"description" => __("Enter the url where you would like to redirect the user after successfully added to the list.","convertplug_vc"),
								"group" => "Submission",
								"dependency" => Array("element" => "cpvc_after_success", "value" => 'redirect'),
						  	),
						  	array(
								"type" => "checkbox",
								"class" => "",
								"heading" => __("Pass Lead Data To Redirect URL","convertplug_vc"),
								"param_name" => "cpvc_leads_data",
								"value" => array( __( 'Enable', 'convertplug_vc' ) => true ),
								"description" => __("Passes the lead email (and name if enabled) as query arguments to redirect URL.","convertplug_vc"),
								"group" => "Submission",
								"dependency" => Array("element" => "cpvc_after_success", "value" => 'redirect'),
						  	),
						  	array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Message After Failed Submission","convertplug_vc"),
								"param_name" => "cpvc_failure_msg",
								"value" => __("Please enter correct email address.","convertplug_vc"),
								"description" => __("Enter the message you would like to display the user if Submission failed.","convertplug_vc"),
								"group" => "Submission",
						  	),
						  	array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Form Style","convertplug_vc"),
								"param_name" => "cpvc_form_style",
								"value" => array(
										'Style 1' => 'style_1',
										'Style 2' => 'style_2',
										'Style 3' => 'style_3',
									),
								"description" => __("","convertplug_vc"),
								"group" => "General",
						  	),
						  	array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Form Text Alignment","convertplug_vc"),
								"param_name" => "cpvc_form_text_align",
								"value" => array(
										'Left' => 'left',
										'Right' => 'right',
										'Center' => 'center',
									),
								"description" => __("","convertplug_vc"),
								"group" => "General",
						  	),
						  	array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Input Box Text Color","convertplug_vc"),
								"param_name" => "cpvc_input_text_color",
								"value" => "#686868",
								"description" => __("","convertplug_vc"),
								"group" => "General"
						  	),
						  	array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Input Box Background Color","convertplug_vc"),
								"param_name" => "cpvc_input_bg_color",
								"value" => "#f7f7f7",
								"description" => __("","convertplug_vc"),
								"group" => "General",
								"dependency" => Array("element" => "cpvc_form_style", "value" => 'style_1'),
						  	),
						  	array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Input Box Border Color","convertplug_vc"),
								"param_name" => "cpvc_input_border_color",
								"value" => "#d1d1d1",
								"description" => __("","convertplug_vc"),
								"group" => "General"
						  	),
						  	array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Input Box Font Size","convertplug_vc"),
								"param_name" => "cpvc_input_font_size",
								"value" => "13",
								"description" => __("","convertplug_vc"),
								"group" => "General",
						  	),
						  	array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Input Box Vertical Padding","convertplug_vc"),
								"param_name" => "cpvc_input_vertical_pedding",
								"value" => "7",
								"description" => __("","convertplug_vc"),
								"group" => "General",
						  	),
						  	array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Input Box Horizontal Padding","convertplug_vc"),
								"param_name" => "cpvc_input_horizontal_pedding",
								"value" => "10",
								"suffix" => "px",
								"description" => __("","convertplug_vc"),
								"group" => "General",
						  	),
						  	array(
								"type" => "checkbox",
								"class" => "",
								"heading" => __("Label Visibility","convertplug_vc"),
								"param_name" => "cpvc_label_visibility",
								"value" => array( __( 'Enable', 'convertplug_vc' ) => true ),
								"description" => __("","convertplug_vc"),
								"group" => "General",
								"std"	=> "yes",
						  	),
						  	array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Label Text Color","convertplug_vc"),
								"param_name" => "cpvc_label_text_color",
								"value" => "#888888",
								"description" => __("","convertplug_vc"),
								"group" => "General",
								"dependency" => Array("element" => "cpvc_label_visibility", "value" => '1'),
						  	),
						  	array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Label Text Font Size","convertplug_vc"),
								"param_name" => "cpvc_label_font_size",
								"value" => "13",
								"description" => __("","convertplug_vc"),
								"group" => "General",
								"dependency" => Array("element" => "cpvc_label_visibility", "value" => '1'),
						  	),
						  	array(
            					"type" => "css_editor",
            					"heading" => __( "CSS box", "convertplug_vc" ),
            					"param_name" => "cpvc_css",
            					"group" => __( "Design", "convertplug_vc" ),
        					),
						)
					)
				);

				// Text Box
				vc_map(
					array(
					   "name" => __("CP Text Field"),
					   "base" => "cpvc_textfield",
					   "class" => "cpvc_form_textfield_icon",
					   "icon" => "cpvc_form_textfield_icon",
					   "category" => __("ConvertPlug VC Addon","convertplug_vc"),
					   "description" => __("Text Field for CP Form Builder","convertplug_vc"),
					   //"as_child" => array('only' => 'cpvc_custom_form_container'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
					   "show_settings_on_create" => true,
					   "is_container"    => false,
					   "params" => array(
						  	array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Field Label","convertplug_vc"),
								"param_name" => "field_label",
								"value" => "",
								"description" => __("The Field Label defines a label for an < input > element.","convertplug_vc"),
								"group" => "General",
								"admin_label" => true
						  	),
						  	array(
								"type" => "cpvc_name",
								"class" => "",
								"heading" => __("Field Name ( Required )","convertplug_vc"),
								"param_name" => "field_name",
								"value" => "",
								"description" => __("The Field Name attribute specifies the name of < input > element. This attribute is used to reference form data after a form is submitted. Please enter single word, no spaces, no special characters, no start with number. Underscores(_) allowed.","convertplug_vc"),
								"group" => "General",
								"admin_label" => true
						  	),
						  	array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Placeholder","convertplug_vc"),
								"param_name" => "placeholder",
								"value" => "",
								"description" => __("The placeholder attribute specifies a short hint that describes the expected value of an input field (e.g. a sample value or a short description of the expected format).","convertplug_vc"),
								"group" => "General",
						  	),
						  	array(
								"type" => "checkbox",
								"class" => "",
								"heading" => __("","convertplug_vc"),
								"param_name" => "required_field",
								"value" => array( __( 'Required Field', 'convertplug_vc' ) => true ),
								"description" => __("When Required Field is checked, it specifies that an input field must be filled out before submitting the form.","convertplug_vc"),
								"group" => "General",
								"std"	=> "yes"
						  	),
						  	array(
            					"type" => "css_editor",
            					"heading" => __( "CSS box", "convertplug_vc" ),
            					"param_name" => "cpvc_css",
            					"group" => __( "Design", "convertplug_vc" ),
        					),
						),
					)
				);

				// Email
				vc_map(
					array(
					   "name" => __("CP Email"),
					   "base" => "cpvc_emailfield",
					   "class" => "cpvc_form_emailfield_icon",
					   "icon" => "cpvc_form_emailfield_icon",
					   "category" => __("ConvertPlug VC Addon","convertplug_vc"),
					   "description" => __("Email Field for CP Form Builder","convertplug_vc"),
					   //"as_child" => array('only' => 'cpvc_custom_form_container'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
					   "show_settings_on_create" => true,
					   "is_container"    => false,
					   "params" => array(
						  	array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Field Label","convertplug_vc"),
								"param_name" => "field_label",
								"value" => "",
								"description" => __("The Field Label defines a label for an < input > element.","convertplug_vc"),
								"group" => "General",
								"admin_label" => true
						  	),
						  	/*array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Field Name ( Required )","convertplug_vc"),
								"param_name" => "field_name",
								"value" => "",
								"description" => __("The Field Name attribute specifies the name of < input > element. This attribute is used to reference form data after a form is submitted. Please enter single word, no spaces, no special characters, no start with number. Underscores(_) allowed.","convertplug_vc"),
								"group" => "General",
						  	),*/
						  	array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Placeholder","convertplug_vc"),
								"param_name" => "placeholder",
								"value" => "",
								"description" => __("The placeholder attribute specifies a short hint that describes the expected value of an input field (e.g. a sample value or a short description of the expected format).","convertplug_vc"),
								"group" => "General",
						  	),
						  	array(
								"type" => "checkbox",
								"class" => "",
								"heading" => __("","convertplug_vc"),
								"param_name" => "required_field",
								"value" => array( __( 'Required Field', 'convertplug_vc' ) => true ),
								"description" => __("When Required Field is checked, it specifies that an input field must be filled out before submitting the form.","convertplug_vc"),
								"group" => "General",
								"std"	=> "yes"
						  	),
						  	array(
            					"type" => "css_editor",
            					"heading" => __( "CSS box", "convertplug_vc" ),
            					"param_name" => "cpvc_css",
            					"group" => __( "Design", "convertplug_vc" ),
        					),
						),
					)
				);

				// Textarea
				vc_map(
					array(
					   "name" => __("CP Textarea"),
					   "base" => "cpvc_textarea",
					   "class" => "cpvc_form_textarea_icon",
					   "icon" => "cpvc_form_textarea_icon",
					   "category" => __("ConvertPlug VC Addon","convertplug_vc"),
					   "description" => __("Textarea for CP Form Builder","convertplug_vc"),
					   //"as_child" => array('only' => 'cpvc_custom_form_container'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
					   "show_settings_on_create" => true,
					   "is_container"    => false,
					   "params" => array(
						  	array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Field Label","convertplug_vc"),
								"param_name" => "field_label",
								"value" => "",
								"description" => __("The Field Label defines a label for an < input > element.","convertplug_vc"),
								"group" => "General",
								"admin_label" => true
						  	),
						  	array(
								"type" => "cpvc_name",
								"class" => "",
								"heading" => __("Field Name ( Required )","convertplug_vc"),
								"param_name" => "field_name",
								"value" => "",
								"description" => __("The Field Name attribute specifies the name of < input > element. This attribute is used to reference form data after a form is submitted. Please enter single word, no spaces, no special characters, no start with number. Underscores(_) allowed.","convertplug_vc"),
								"group" => "General",
								"admin_label" => true
						  	),
						  	array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Placeholder","convertplug_vc"),
								"param_name" => "placeholder",
								"value" => "",
								"description" => __("The placeholder attribute specifies a short hint that describes the expected value of an input field (e.g. a sample value or a short description of the expected format).","convertplug_vc"),
								"group" => "General",
						  	),
						  	array(
								"type" => "checkbox",
								"class" => "",
								"heading" => __("","convertplug_vc"),
								"param_name" => "required_field",
								"value" => array( __( 'Required Field', 'convertplug_vc' ) => true ),
								"description" => __("When Required Field is checked, it specifies that an input field must be filled out before submitting the form.","convertplug_vc"),
								"group" => "General",
								"std"	=> "yes"
						  	),
						  	array(
            					"type" => "css_editor",
            					"heading" => __( "CSS box", "convertplug_vc" ),
            					"param_name" => "cpvc_css",
            					"group" => __( "Design", "convertplug_vc" ),
        					),
						),
					)
				);

				// Number
				vc_map(
					array(
					   "name" => __("CP Number"),
					   "base" => "cpvc_number",
					   "class" => "cpvc_form_number_icon",
					   "icon" => "cpvc_form_number_icon",
					   "category" => __("ConvertPlug VC Addon","convertplug_vc"),
					   "description" => __("Number Field for CP Form Builder","convertplug_vc"),
					   //"as_child" => array('only' => 'cpvc_custom_form_container'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
					   "show_settings_on_create" => true,
					   "is_container"    => false,
					   "params" => array(
						  	array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Field Label","convertplug_vc"),
								"param_name" => "field_label",
								"value" => "",
								"description" => __("The Field Label defines a label for an < input > element.","convertplug_vc"),
								"group" => "General",
								"admin_label" => true
						  	),
						  	array(
								"type" => "cpvc_name",
								"class" => "",
								"heading" => __("Field Name ( Required )","convertplug_vc"),
								"param_name" => "field_name",
								"value" => "",
								"description" => __("The Field Name attribute specifies the name of < input > element. This attribute is used to reference form data after a form is submitted. Please enter single word, no spaces, no special characters, no start with number. Underscores(_) allowed.","convertplug_vc"),
								"group" => "General",
								"admin_label" => true
						  	),
						  	array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Placeholder","convertplug_vc"),
								"param_name" => "placeholder",
								"value" => "",
								"description" => __("The placeholder attribute specifies a short hint that describes the expected value of an input field (e.g. a sample value or a short description of the expected format).","convertplug_vc"),
								"group" => "General",
						  	),
						  	array(
								"type" => "checkbox",
								"class" => "",
								"heading" => __("","convertplug_vc"),
								"param_name" => "required_field",
								"value" => array( __( 'Required Field', 'convertplug_vc' ) => true ),
								"description" => __("When Required Field is checked, it specifies that an input field must be filled out before submitting the form.","convertplug_vc"),
								"group" => "General",
								"std"	=> "yes"
						  	),
						  	array(
            					"type" => "css_editor",
            					"heading" => __( "CSS box", "convertplug_vc" ),
            					"param_name" => "cpvc_css",
            					"group" => __( "Design", "convertplug_vc" ),
        					),
						),
					)
				);

				// Dropdown
				vc_map(
					array(
					   "name" => __("CP Dropdown"),
					   "base" => "cpvc_dropdown",
					   "class" => "cpvc_form_dropdown_icon",
					   "icon" => "cpvc_form_dropdown_icon",
					   "category" => __("ConvertPlug VC Addon","convertplug_vc"),
					   "description" => __("Select Box for CP Form Builder","convertplug_vc"),
					   //"as_child" => array('only' => 'cpvc_custom_form_container'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
					   "show_settings_on_create" => true,
					   "is_container"    => false,
					   "params" => array(
						  	array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Field Label","convertplug_vc"),
								"param_name" => "field_label",
								"value" => "",
								"description" => __("The Field Label defines a label for an < input > element.","convertplug_vc"),
								"group" => "General",
								"admin_label" => true
						  	),
						  	array(
								"type" => "cpvc_name",
								"class" => "",
								"heading" => __("Field Name ( Required )","convertplug_vc"),
								"param_name" => "field_name",
								"value" => "",
								"description" => __("","convertplug_vc"),
								"group" => "General",
								"admin_label" => true
						  	),
						  	array(
								"type" => "textarea",
								"class" => "",
								"heading" => __("Dropdown Choice Options","convertplug_vc"),
								"param_name" => "dd_options",
								"value" => "",
								"description" => __("Enter the options for your dropdown list. Enter each option on new line.","convertplug_vc"),
								"group" => "General",
						  	),
						  	array(
								"type" => "checkbox",
								"class" => "",
								"heading" => __("","convertplug_vc"),
								"param_name" => "required_field",
								"value" => array( __( 'Required Field', 'convertplug_vc' ) => true ),
								"description" => __("When Required Field is checked, it specifies that an input field must be filled out before submitting the form.","convertplug_vc"),
								"group" => "General",
								"std"	=> "yes"
						  	),
						  	array(
            					"type" => "css_editor",
            					"heading" => __( "CSS box", "convertplug_vc" ),
            					"param_name" => "cpvc_css",
            					"group" => __( "Design", "convertplug_vc" ),
        					),
						),
					)
				);

				// Hidden
				vc_map(
					array(
					   "name" => __("CP Hidden Field"),
					   "base" => "cpvc_hidden_field",
					   "class" => "cpvc_form_hiddenfield_icon",
					   "icon" => "cpvc_form_hiddenfield_icon",
					   "category" => __("ConvertPlug VC Addon","convertplug_vc"),
					   "description" => __("Hidden Field for CP Form Builder","convertplug_vc"),
					   //"as_child" => array('only' => 'cpvc_custom_form_container'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
					   "show_settings_on_create" => true,
					   "is_container"    => false,
					   "params" => array(
						  	array(
								"type" => "cpvc_name",
								"class" => "",
								"heading" => __("Field Name ( Required )","convertplug_vc"),
								"param_name" => "field_name",
								"value" => "",
								"description" => __("The Field Name attribute specifies the name of < input > element. This attribute is used to reference form data after a form is submitted. Please enter single word, no spaces, no special characters, no start with number. Underscores(_) allowed.","convertplug_vc"),
								"group" => "General",
								"admin_label" => true
						  	),
						  	array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Field Value","convertplug_vc"),
								"param_name" => "field_value",
								"value" => "",
								"description" => __("","convertplug_vc"),
								"group" => "General",
						  	),
						),
					)
				);

				// Submit
				vc_map(
					array(
					   "name" => __("CP Submit"),
					   "base" => "cpvc_submit_button",
					   "class" => "cpvc_form_submit_icon",
					   "icon" => "cpvc_form_submit_icon",
					   "category" => __("ConvertPlug VC Addon","convertplug_vc"),
					   "description" => __("Submit Button for CP Form Builder","convertplug_vc"),
					   //"as_child" => array('only' => 'cpvc_custom_form_container'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
					   "show_settings_on_create" => true,
					   "is_container"    => false,
					   "params" => array(
						  	array(
								"type" => "textfield",
								"class" => "",
								"heading" => __("Submit Button Text","convertplug_vc"),
								"param_name" => "cpvc_button_text",
								"value" => __("Subscribe","convertplug_vc"),
								"description" => __("","convertplug_vc"),
								"group" => "General",
								"admin_label" => true
						  	),
						  	array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Submit Button Text Alignment","convertplug_vc"),
								"param_name" => "cpvc_button_txt_align",
								"value" => array(
										'Center' => 'center',
										'Left' => 'left',
										'Right' => 'right',
									),
								"description" => __("","convertplug_vc"),
								"group" => "General",
						  	),
						  	array(
								"type" => "dropdown",
								"class" => "",
								"heading" => __("Submit Button Style","convertplug_vc"),
								"param_name" => "cpvc_button_style",
								"value" => array(
										'Flat' => 'flat',
										'3D' => '3d',
										'Outline' => 'outline',
										'Gradient' => 'gradient'
									),
								"description" => __("","convertplug_vc"),
								"group" => "General",
						  	),
						  	array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Submit Button Text Color","convertplug_vc"),
								"param_name" => "cpvc_btn_text_color",
								"value" => "#ffffff",
								"description" => __("","convertplug_vc"),
								"group" => "General"
						  	),
						  	array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Submit Button Text Hover Color","convertplug_vc"),
								"param_name" => "cpvc_btn_text_hover_color",
								"value" => "#1e73be",
								"description" => __("","convertplug_vc"),
								"group" => "General",
								"dependency" => Array("element" => "cpvc_button_style", "value" => 'outline'),
						  	),
						  	array(
								"type" => "colorpicker",
								"class" => "",
								"heading" => __("Submit Button Background Color","convertplug_vc"),
								"param_name" => "cpvc_btn_bg_color",
								"value" => "#1e73be",
								"description" => __("","convertplug_vc"),
								"group" => "General"
						  	),
						  	array(
            					"type" => "css_editor",
            					"heading" => __( "CSS box", "convertplug_vc" ),
            					"param_name" => "cpvc_css",
            					"group" => __( "Design", "convertplug_vc" ),
        					),
						),
					)
				);
			}
		}

		/* CP Custom Form */
		function cpvc_custom_form_container_callback( $atts,$content = null ) {
			$output = $cpvc_select_campaign = $cpvc_after_success = $cpvc_success_msg =
			$cpvc_failure_msg = $cpvc_redirect_url = $cpvc_leads_data = $cpvc_form_style = $cpvc_form_text_align =
			$cpvc_input_text_color = $cpvc_input_bg_color = $cpvc_input_border_color = $cpvc_input_font_size =
			$cpvc_input_vertical_pedding = $cpvc_input_horizontal_pedding = $cpvc_label_visibility = $cpvc_leads_data = $cpvc_label_text_color = $cpvc_label_font_size = $cpvc_redirect_url_only = $cpvc_target_redirect = '';

			extract(shortcode_atts(array(
				'cpvc_select_campaign' 			=> 'no-list',
				'cpvc_after_success' 			=> 'message',
				'cpvc_success_msg' 				=> 'Thank you.',
				'cpvc_failure_msg' 				=> 'Please enter correct email address.',
				'cpvc_redirect_url' 			=> '#',
				'cpvc_leads_data' 				=> '0',
				'cpvc_form_style' 				=> 'style_1',
				'cpvc_form_text_align' 			=> 'left',
				'cpvc_input_text_color' 		=> '#686868',
				'cpvc_input_bg_color' 			=> '#f7f7f7',
				'cpvc_input_border_color' 		=> '#d1d1d1',
				'cpvc_input_font_size' 			=> '13',
				'cpvc_input_vertical_pedding' 	=> '7',
				'cpvc_input_horizontal_pedding' => '10',
				'cpvc_label_visibility' 		=> '1',
				'cpvc_label_text_color'			=> '#888888',
				'cpvc_label_font_size'			=> '13',
				'cpvc_css'						=> '',
			),$atts));

			if( $cpvc_select_campaign == 'no-list' ) {

			}
			else {
				/* Get decoded URL String */
				$cpvc_redirect_url = explode( '|', $cpvc_redirect_url );
				if( is_array( $cpvc_redirect_url ) ) {
                    foreach ( $cpvc_redirect_url as $key => $value ) {
                        $val = explode( ":", $value );

                        if( $val[0] == 'url' ) {
                            $cpvc_redirect_url_only = urldecode($val[1]);
                        }

                        if( $val[0] == 'target' ) {
                            $cpvc_target_redirect = urldecode($val[1]);
                        }
                    }
                }

				$container_id = 'cpvc_custom_styles_'.rand(1000000,9999999);
				/* Input Box Styling */
				$cpvc_input_text_color = (trim($cpvc_input_text_color) != "") ? $cpvc_input_text_color : '#686868';
				$cpvc_input_bg_color = (trim($cpvc_input_bg_color) != "") ? $cpvc_input_bg_color : '#f7f7f7';
				$cpvc_input_border_color = (trim($cpvc_input_border_color) != "") ? $cpvc_input_border_color : '#d1d1d1';
				$cpvc_input_font_size = (trim($cpvc_input_font_size) != "") ? $cpvc_input_font_size : '13';
				$cpvc_input_vertical_pedding = (trim($cpvc_input_vertical_pedding) != "") ? $cpvc_input_vertical_pedding : '7';
				$cpvc_input_horizontal_pedding = (trim($cpvc_input_horizontal_pedding) != "") ? $cpvc_input_horizontal_pedding : '10';

				$cpvc_label_text_color = (trim($cpvc_label_text_color) != "") ? $cpvc_label_text_color : '#888888';
				$cpvc_label_font_size = (trim($cpvc_label_font_size) != "") ? $cpvc_label_font_size : '13';

				$input_styles = '';
				$input_styles .= 'color: '. $cpvc_input_text_color .';';
				$input_styles .= 'background-color: '. $cpvc_input_bg_color .';';
				$input_styles .= 'border-color: '. $cpvc_input_border_color .';';
				$input_styles .= 'font-size: '. $cpvc_input_font_size .'px;';
				$input_styles .= 'padding: '. $cpvc_input_vertical_pedding .'px '. $cpvc_input_horizontal_pedding .'px;';

				/* Get Label Visibility */
				$cpvc_label_visibility = ($cpvc_label_visibility != '1') ? 'cpvc_hide_labels' : '';

				$cpvc_css = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $cpvc_css, ' ' ), "cpvc_form_container", $atts );

				/* Get Hidden Fields */
				$hidden_fields = $this->cpvc_addon_get_form_hidden_fields($cpvc_select_campaign);
	    		$on_success_action 	= ($cpvc_after_success == "redirect") ? $cpvc_redirect_url_only : esc_attr( stripslashes( $cpvc_success_msg ));
		        $hidden_fields .= '<input type="hidden" name="msg_wrong_email" value="'. $cpvc_failure_msg .'" />';
		        $hidden_fields .= '<input type="hidden" name="'.$cpvc_after_success.'" value="'. $on_success_action .'" />';

				$output .= '<div class="cpvc-custom-form-container cpvc-'.$cpvc_form_style.' '. $cpvc_css .' cpvc_form_text_align-'. $cpvc_form_text_align .' '. $cpvc_label_visibility .' '. $container_id .'" data-redirect-lead-data="'. $cpvc_leads_data .'" data-custom_styles="'. $input_styles .'" data-custom_class="'. $container_id .'" data-shadow_color="'. $cpvc_input_border_color .'" data-text_color="'. $cpvc_input_text_color .'" data-label_text_color="'. $cpvc_label_text_color .'" data-label_font_size="'. $cpvc_label_font_size .'" data-input_vertical_pedding="'. $cpvc_input_vertical_pedding .'" data-input_horizontal_pedding="'. $cpvc_input_horizontal_pedding .'" data-input_font_size="'. $cpvc_input_font_size .'">
							<div class="cpvc-custom-form-content form-main cpvc-form-layout" data-redirect-target="'. $cpvc_target_redirect .'">
								<form id="cpvc-smile-optin-form" class="cpvc-form ">';

				$output .= $hidden_fields;

				$output .= do_shortcode($content);

				$output .= '	</form><!-- #cpvc-smile-optin-form -->
							</div>
							<div class="cpvc-addon-form-processing-wrap" style="">
								<div class="cpvc-addon-form-after-submit">
			                		<div class="cpvc-addon-form-processing" style="">
			                			<div class="cpvc-smile-absolute-loader" style="visibility: visible;">
									        <div class="cpvc-smile-loader">
									            <div class="cpvc-smile-loading-bar"></div>
									            <div class="cpvc-smile-loading-bar"></div>
									            <div class="cpvc-smile-loading-bar"></div>
									            <div class="cpvc-smile-loading-bar"></div>
									        </div>
									    </div>
			                		</div>
			                		<div class="cpvc-addon-msg-on-submit"></div>
			                	</div>
			                </div>
	    					</div>';
			}

			return $output;
		}

		/* CP Text Field Shortcode */
		function cpvc_textfield_callback( $atts ) {
			$output = $field_label = $field_name = $placeholder = $required_field =  '';

			extract(shortcode_atts(array(
				'field_label' 		=> '',
				'field_name' 		=> '',
				'placeholder' 		=> '',
				'required_field' 	=> '1',
				'cpvc_css'			=> '',
			),$atts));

			$cpvc_css = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $cpvc_css, ' ' ), "ult_team", $atts );

			$field_name = preg_replace('/[^A-Za-z0-9_]/', '', $field_name);
			$required = ($required_field == 1 ) ? 'required="required"' : '';
			$field_name = (trim($field_name) != "") ? $field_name : '_BLANK_NAME';

			$random_id = 'param['. $field_name . rand(100,999999).']';
			$output .= '<div class="cpvc-form-field ">
			                <label for="'. $random_id .'">'. $field_label .'</label>
			                <div>
			                	<input class="cpvc-input cpvc-textfeild cpvc-input-'. $field_name .' '.$cpvc_css.'" type="text" id="'. $random_id .'" name="param['. $field_name .']" placeholder="'. $placeholder .'" '. $required .' />
			                </div>
			            </div><!-- .cpvc-form-field -->';
			return $output;
		}

		/* CP Email Shortcode */
		function cpvc_emailfield_callback( $atts ) {
			$output = $field_label = $placeholder = $required_field =  '';

			extract(shortcode_atts(array(
				'field_label' 		=> '',
				//'field_name' 		=> '',
				'placeholder' 		=> '',
				'required_field' 	=> '1',
				'cpvc_css'			=> '',
			),$atts));

			$cpvc_css = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $cpvc_css, ' ' ), "cpvc_emailfield", $atts );

			//$field_name = preg_replace('/[^A-Za-z0-9_]/', '', $field_name);
			$random_id = 'param[email'. rand(100,999999).']';
			$required = ($required_field == 1 ) ? 'required="required"' : '';
			$output .= '<div class="cpvc-form-field ">
			                <label for="'. $random_id .'" >'. $field_label .'</label>
			                <div>
			                	<input class="cpvc-input cpvc-email cpvc-input-email '. $cpvc_css .'" type="email" id="'. $random_id .'" name="param[email]" placeholder="'. $placeholder .'" '. $required .' />
			                </div>
			            </div><!-- .cpvc-form-field -->';
			return $output;
		}

		/* CP Textarea Shortcode */
		function cpvc_textarea_callback( $atts ) {
			$output = $field_label = $field_name = $placeholder = $required_field =  '';

			extract(shortcode_atts(array(
				'field_label' 		=> '',
				'field_name' 		=> '',
				'placeholder' 		=> '',
				'required_field' 	=> '1',
				'cpvc_css'			=> '',
			),$atts));

			$cpvc_css = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $cpvc_css, ' ' ), "cpvc_textarea", $atts );

			$random_id = 'param['. $field_name . rand(100,999999).']';

			$field_name = preg_replace('/[^A-Za-z0-9_]/', '', $field_name);
			$field_name = (trim($field_name) != "") ? $field_name : '_BLANK_NAME';
			$required = ($required_field == 1 ) ? 'required="required"' : '';
			$output .= '<div class="cpvc-form-field ">
			                <label for="'. $random_id .'">'. $field_label .'</label>
			                <div>
			                	<textarea class="cpvc-input cpvc-textarea cpvc-input-'. $field_name .' '. $cpvc_css .'" id="'. $random_id .'" name="param['. $field_name .']" placeholder="'. $placeholder .'" '. $required .' ></textarea>
			                </div>
			            </div><!-- .cpvc-form-field -->';
			return $output;
		}

		/* CP Number Shortcode */
		function cpvc_number_callback( $atts ) {
			$output = $field_label = $field_name = $placeholder = $required_field =  '';

			extract(shortcode_atts(array(
				'field_label' 		=> '',
				'field_name' 		=> '',
				'placeholder' 		=> '',
				'required_field' 	=> '1',
				'cpvc_css'			=> '',
			),$atts));

			$cpvc_css = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $cpvc_css, ' ' ), "cpvc_number", $atts );
			$random_id = 'param['. $field_name . rand(100,999999).']';
			$field_name = preg_replace('/[^A-Za-z0-9_]/', '', $field_name);
			$field_name = (trim($field_name) != "") ? $field_name : '_BLANK_NAME';
			$required = ($required_field == 1 ) ? 'required="required"' : '';
			$output .= '<div class="cpvc-form-field ">
			                <label for="'. $random_id .'">'. $field_label .'</label>
			                <div>
			                	<input class="cpvc-input cpvc-number cpvc-input-'. $field_name .' '. $cpvc_css .'" type="number" id="'. $random_id .'" name="param['. $field_name .']" placeholder="'. $placeholder .'" '. $required .' />
			                </div>
			            </div><!-- .cpvc-form-field -->';
			return $output;
		}

		/* CP Dropdown Shortcode */
		function cpvc_dropdown_callback( $atts ) {
			$output = $field_label = $field_name = $dd_options = $required_field =  '';

			extract(shortcode_atts(array(
				'field_label' 		=> '',
				'field_name' 		=> '',
				'dd_options' 		=> '',
				'required_field' 	=> '1',
				'cpvc_css'			=> '',
			),$atts));

			$cpvc_css = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $cpvc_css, ' ' ), "cpvc_dropdown", $atts );

			$dd_options = explode(chr(10), strip_tags($dd_options));

			$random_id = 'param['. $field_name . rand(100,999999).']';
			$field_name = preg_replace('/[^A-Za-z0-9_]/', '', $field_name);
			$field_name = (trim($field_name) != "") ? $field_name : '_BLANK_NAME';
			$required = ($required_field == 1 ) ? 'required="required"' : '';
			$output .= '<div class="cpvc-form-field ">
			                <label for="'. $random_id .'">'. $field_label .'</label>
			                <div>
			                	<select class="cpvc-input cpvc-dropdown cpvc-input-'. $field_name .' '. $cpvc_css .'" id="'. $random_id .'" name="param['. $field_name .']" '. $required .'>';
          	foreach ($dd_options as $value) {
            			$output .=  '<option value="'. $value .'">'. $value .'</option>';
            }
			$output .= 			'</select>
			                </div>
			            </div><!-- .cpvc-form-field -->';
			return $output;
		}

		/* CP Hidden Field Shortcode */
		function cpvc_hidden_field_callback( $atts ) {
			$output = $field_name = $field_value = '';

			extract(shortcode_atts(array(
				'field_name' 		=> '',
				'field_value' 		=> '',
			),$atts));

			$field_name = preg_replace('/[^A-Za-z0-9_]/', '', $field_name);
			$field_name = (trim($field_name) != "") ? $field_name : '_BLANK_NAME';
			$output .= '<div class="cpvc-form-field ">
			                <div>
			                	<input class="cpvc-input cpvc-hidden cpvc-input-'. $field_name .'" type="hidden" name="param['. $field_name .']" value="'. $field_value .'" />
			                </div>
			            </div><!-- .cpvc-form-field -->';
			return $output;
		}

		/* CP Submit Button Shortcode */
		function cpvc_submit_button_callback ( $atts ) {
			$output = $cpvc_button_text =
			$cpvc_btn_bg_color = $cpvc_button_style = $cpvc_btn_text_hover_color = '';

			extract(shortcode_atts(array(
				'cpvc_button_text' 			=> 'Subscribe',
				'cpvc_btn_bg_color' 			=> '#1e73be',
				'cpvc_btn_text_color' 		=> '#ffffff',
				'cpvc_button_style' 			=> 'flat',
				'cpvc_button_txt_align'		=> 'center',
				'cpvc_btn_text_hover_color'	=> '#1e73be',
				'cpvc_css'					=> '',
			),$atts));

			$cpvc_css = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $cpvc_css, ' ' ), "cpvc_submit_button", $atts );

			$cpvc_submit_button_id = 'cpvc-addon-'.rand(1000000,9999999);

			$cpvc_btn_bg_color = trim($cpvc_btn_bg_color) != "" ? $cpvc_btn_bg_color : '#1e73be';
			$cpvc_btn_text_color = trim($cpvc_btn_text_color) != "" ? $cpvc_btn_text_color : '#ffffff';
			$cpvc_btn_text_hover_color = trim($cpvc_btn_text_hover_color) != "" ? $cpvc_btn_text_hover_color : '#1e73be';

			$dark_bg_color = $this->get_dark_color( $cpvc_btn_bg_color );
			$dark_bg_color = 'rgb('.$dark_bg_color.')';

			/* Inline Style for Button */
			if( $cpvc_button_style == 'flat' ) {

				$output .= '<style>
								.cpvc-form-field .'. $cpvc_submit_button_id.' button.cpvc-submit-btn-flat {
									color:'.$cpvc_btn_text_color.';
									background:'.$cpvc_btn_bg_color.';
									text-align:'.$cpvc_button_txt_align.';
									text-align-last:'.$cpvc_button_txt_align.';
								}
								.cpvc-form-field .'. $cpvc_submit_button_id.' button.cpvc-submit-btn-flat:hover {
									background: none !important;
									background:'.$dark_bg_color.' !important;
								}
							</style>';
			} elseif( $cpvc_button_style == '3d' ) {
				$output .= '<style>
								.cpvc-form-field .'. $cpvc_submit_button_id.' button.cpvc-submit-btn-3d {
									color:'.$cpvc_btn_text_color.';
									background:'.$cpvc_btn_bg_color.';
									box-shadow: 0 6px '.$dark_bg_color.';
									position: relative;
									text-align:'.$cpvc_button_txt_align.';
									text-align-last:'.$cpvc_button_txt_align.';
								}
								.cpvc-form-field .'. $cpvc_submit_button_id.' button.cpvc-submit-btn-3d:hover {
								    box-shadow: 0 4px '.$dark_bg_color.';
									top: 2px;
								}
								.cpvc-form-field .'. $cpvc_submit_button_id.' button.cpvc-submit-btn-3d:active {
								    box-shadow: 0 0px '.$dark_bg_color.';
									top: 6px;
								}
							</style>';
			} elseif( $cpvc_button_style == 'outline' ) {
				$output .= '<style>
								.cpvc-form-field .'. $cpvc_submit_button_id.' button.cpvc-submit-btn-outline {
									color: '. $cpvc_btn_text_color .';
									background: transparent;
								    border: 2px solid '. $cpvc_btn_bg_color .';
									text-align:'.$cpvc_button_txt_align.';
									text-align-last:'.$cpvc_button_txt_align.';
								}
								.cpvc-form-field .'. $cpvc_submit_button_id.' button.cpvc-submit-btn-outline:hover {
									background:'.$dark_bg_color.' !important;
								    border: 2px solid '. $dark_bg_color .';
								    color: '. $cpvc_btn_text_hover_color .';
								}
							</style>';
			} elseif ( $cpvc_button_style == 'gradient' ) {
				$output .= '<style>
								.cpvc-form-field .'. $cpvc_submit_button_id.' button.cpvc-submit-btn-gradient {
									color:'.$cpvc_btn_text_color.';
									border: none;
								    background: -webkit-linear-gradient('. $cpvc_btn_bg_color .', '. $dark_bg_color .');
								    background: -o-linear-gradient('. $cpvc_btn_bg_color .', '. $dark_bg_color .');
								    background: -moz-linear-gradient('. $cpvc_btn_bg_color .', '. $dark_bg_color .');
								    background: linear-gradient('. $cpvc_btn_bg_color .', '. $dark_bg_color .');
								    text-align:'.$cpvc_button_txt_align.';
									text-align-last:'.$cpvc_button_txt_align.';
								}

								.cpvc-form-field .'. $cpvc_submit_button_id.' button.cpvc-submit-btn-gradient:hover {
									background:'.$dark_bg_color.' !important;
								}
							</style>';
			}

			$output .= '<div class="cpvc-form-field ">
			                <div class="' . $cpvc_submit_button_id . '">
			                	<button class="cpvc-input cpvc-submit-button btn btn-subscribe cpvc-submit-btn-'. $cpvc_button_style .' '. $cpvc_css .'" type="button">
									<span class="cpvc-addon_responsive cpvc-addon_font">' . $cpvc_button_text. '</span>
								</button>
			                </div>
			            </div><!-- .cpvc-form-field -->';
			return $output;
		}

	}

	new ConvertPlug_VC();

	add_action( 'vc_after_init', 'ConvertPlug_VC_Shortcodes' );
	function ConvertPlug_VC_Shortcodes () {
		/* CP Inline Display */
		if ( class_exists( 'WPBakeryShortCode' ) ) {
		    class WPBakeryShortCode_cpvc_inline extends WPBakeryShortCode {
		    }
		}

		/* CP Custom Form */
		if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
		    class WPBakeryShortCode_cpvc_custom_form_container extends WPBakeryShortCodesContainer {
		    }
		}
		if ( class_exists( 'WPBakeryShortCode' ) ) {
		    class WPBakeryShortCode_cpvc_textfield extends WPBakeryShortCode {
		    }
		}
		if ( class_exists( 'WPBakeryShortCode' ) ) {
		    class WPBakeryShortCode_cpvc_emailfield extends WPBakeryShortCode {
		    }
		}
		if ( class_exists( 'WPBakeryShortCode' ) ) {
		    class WPBakeryShortCode_cpvc_textarea extends WPBakeryShortCode {
		    }
		}
		if ( class_exists( 'WPBakeryShortCode' ) ) {
		    class WPBakeryShortCode_cpvc_number extends WPBakeryShortCode {
		    }
		}
		if ( class_exists( 'WPBakeryShortCode' ) ) {
		    class WPBakeryShortCode_cpvc_dropdown extends WPBakeryShortCode {
		    }
		}
		if ( class_exists( 'WPBakeryShortCode' ) ) {
		    class WPBakeryShortCode_cpvc_hidden_field extends WPBakeryShortCode {
		    }
		}
		if ( class_exists( 'WPBakeryShortCode' ) ) {
		    class WPBakeryShortCode_cpvc_submit_button extends WPBakeryShortCode {
		    }
		}
	}
}

/**
 * Load brainstorm product updater
 */
$bsf_core_version_file = realpath( dirname( __FILE__ ) . '/admin/bsf-core/version.yml' );

if ( is_file( $bsf_core_version_file ) ) {
	global $bsf_core_version, $bsf_core_path;
	$bsf_core_dir = realpath( dirname( __FILE__ ) . '/admin/bsf-core/' );
	$version      = file_get_contents( $bsf_core_version_file );
	if ( version_compare( $version, $bsf_core_version, '&gt;' ) ) {
		$bsf_core_version = $version;
		$bsf_core_path    = $bsf_core_dir;
	}
}

if ( ! function_exists( 'bsf_core_load' ) ) {
	function bsf_core_load() {
		global $bsf_core_version, $bsf_core_path;
		if ( is_file( realpath( $bsf_core_path . '/index.php' ) ) ) {
			include_once realpath( $bsf_core_path . '/index.php' );
		}
	}
}
add_action( 'init', 'bsf_core_load', 999 );
