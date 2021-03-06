<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/*
* Add-on Name: DFD Call To Action for Visual Composer
*/

class WPBakeryShortCode_Dfd_Call_To_Action extends WPBakeryShortCode {}

$module_images = DFD_EXTENSIONS_PLUGIN_URL .'vc_custom/admin/img/call_to_action/';
vc_map(
	array(
		'name'					=> esc_html__('Call To Action', 'dfd-native'),
		'base'					=> 'dfd_call_to_action',
		'class'					=> 'dfd_call_to_action dfd_shortcode',
		'icon'					=> 'dfd_call_to_action dfd_shortcode',
		'category'				=> esc_html__('Native', 'dfd-native'),
		'params'				=> array(
			array(
				'heading'			=> esc_html__('Style', 'dfd-native'),
				'type'				=> 'radio_image_select',
				'param_name'		=> 'main_style',
				'simple_mode'		=> false,
				'options'			=> array(
					'style-1'			=> array(
						'tooltip'			=> esc_html__('Tilt right','dfd-native'),
						'src'				=> $module_images.'style-1.png'
					),
					'style-2'			=> array(
						'tooltip'			=> esc_html__('Tilt left','dfd-native'),
						'src'				=> $module_images.'style-2.png'
					),
				),
			),
			array(
				'type'             => 'dfd_heading_param',
				'text'             => esc_html__( 'Extra features', 'dfd-native' ),
				'param_name'       => 'extra_features_elements_heading',
				'edit_field_class' => 'dfd-heading-param-wrapper vc_column vc_col-sm-12',
			),
			array(
				'type'				=> 'dropdown',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Choose the appear effect for the element','dfd-native').'</span></span>'.esc_html__('Animation', 'dfd-native'),
				'param_name'		=> 'module_animation',
				'value'				=> Dfd_Theme_Helpers::dfd_module_animation_styles(),
			),
			array(
				'type'				=> 'textfield',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Add the unique class name for the element which can be used for custom CSS codes','dfd-native').'</span></span>'.esc_html__('Custom CSS Class', 'dfd-native'),
				'param_name'		=> 'el_class',
				'edit_field_class'	=> 'vc_col-sm-12 no-border-bottom',
			),
			array(
			'type'				=> 'dfd_video_link_param',
			'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Video tutorial and theme documentation article','dfd-native').'</span></span>'.esc_html__('Tutorials','dfd-native'),
			'param_name'		=> 'tutorials',
			'doc_link'			=> '//nativewptheme.net/support/visual-composer/call-to-action',
			'video_link'		=> 'https://youtu.be/wr8q--5C_TM',
			),
			array(
				'type'				=> 'dfd_single_checkbox',
				'heading'			=> '<span class="dfd-vc-toolip tooltip-bottom"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('This option allows you to add the icon to the call to action content','dfd-native').'</span></span>'.esc_html__('Icon', 'dfd-native'),
				'param_name'		=> 'show_icon',
				'options'			=> array(
					'enable_icon'		=> array(
						'on'				=> esc_attr__('Yes', 'dfd-native'),
						'off'				=> esc_attr__('No', 'dfd-native'),
					),
				),
				'group'				=> esc_html__('Main content', 'dfd-native'),
			),
			array(
				'type'				=> 'dfd_radio_advanced',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Use the existing icon font, upload custom image or add the text', 'dfd-native').'</span></span>'.esc_html__('Icon to display', 'dfd-native'),
				'param_name'		=> 'icon_type',
				'value'				=> 'selector',
				'options'			=> array(
					esc_html__('Icon', 'dfd-native')	=> 'selector',
					esc_html__('Image', 'dfd-native')	=> 'custom',
					esc_html__('Text', 'dfd-native')   => 'text',
				),
				'dependency'		=> array('element' => 'show_icon', 'value' => array('enable_icon')),
				'group'				=> esc_html__('Main content', 'dfd-native'),
			),
			array(
				'type'				=> 'dfd_radio_advanced',
				'heading'			=> esc_html__('Icon library', 'dfd-native'),
				'param_name'		=> 'select_icon',
				'value'				=> 'dfd_icons',
				'options'			=> Dfd_Theme_Helpers::build_vc_icons_fonts_list(false),
				'dependency'		=> array('element' => 'icon_type', 'value' => array('selector')),
				'group'				=> esc_html__('Main content', 'dfd-native'),
			),
			array(
				'type'				=> 'iconpicker',
				'heading'			=> esc_html__('Select Icon ', 'dfd-native'),
				'param_name'		=> 'ic_dfd_icons',
				'value'				=> 'dfd-socicon-px-icon',
				'settings'			=> array(
					'emptyIcon'			=> false,
					'type'				=> 'dfd_icons',
					'iconsPerPage'		=> 4000,
				),
				'dependency'		=> array('element' => 'select_icon', 'value' => 'dfd_icons',),
				'group'				=> esc_html__('Main content', 'dfd-native'),
			),
			Dfd_Theme_Helpers::build_vc_icons_param('fontawesome', esc_html__('Main content', 'dfd-native'), array()),
			Dfd_Theme_Helpers::build_vc_icons_param('openiconic', esc_html__('Main content', 'dfd-native'), array()),
			Dfd_Theme_Helpers::build_vc_icons_param('typicons', esc_html__('Main content', 'dfd-native'), array()),
			Dfd_Theme_Helpers::build_vc_icons_param('entypo', esc_html__('Main content', 'dfd-native'), array()),
			Dfd_Theme_Helpers::build_vc_icons_param('linecons', esc_html__('Main content', 'dfd-native'), array()),
			array(
				'type'				=> 'attach_image',
				'heading'			=> esc_html__('Upload Image:', 'dfd-native'),
				'param_name'		=> 'icon_image_id',
				'description'		=> esc_html__('Upload the custom image icon.', 'dfd-native'),
				'dependency'		=> array('element' => 'icon_type', 'value' => array('custom')),
				'group'				=> esc_html__('Main content', 'dfd-native'),
			),
			array(
				'type'				=> 'textfield',
				'heading'			=> esc_html__('Text', 'dfd-native'),
				'param_name'		=> 'icon_text',
				'dependency'		=> array('element' => 'icon_type', 'value' => array('text')),
				'group'				=> esc_html__('Main content', 'dfd-native'),
			),
			array(
				'type'				=> 'number',
				'heading'			=> esc_html__('Icon size', 'dfd-native'),
				'param_name'		=> 'icon_size',
				'value'				=> 40,
				'edit_field_class'	=> 'vc_column vc_col-sm-6 dfd-number-wrap crum_vc',
				'dependency'		=> array('element' => 'icon_type', 'value'   => array('custom', 'selector')),
				'group'				=> esc_html__('Main content', 'dfd-native'),
			),
			array(
				'type'				=> 'colorpicker',
				'class'				=> 'crum_vc',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Allows you to choose the color for the icon you have set for the content. Default icon color is #fff','dfd-native').'</span></span>'.esc_html__('Color', 'dfd-native'),
				'param_name'		=> 'icon_color',
				'value'				=> '#ffffff',
				'edit_field_class'	=> 'vc_column vc_col-sm-6 crum_vc',
				'dependency'		=> array('element' => 'icon_type', 'value' => array('selector')),
				'group'				=> esc_html__('Main content', 'dfd-native'),
			),
			array(
				'type'				=> 'dfd_font_container',
				'param_name'		=> 'text_icon_font_options',
				'settings'			=> array(
					'fields'		=> array(
						'font_size',
						'letter_spacing',
						'color',
						'font_style'
					),
				),
				'dependency'		=> array('element' => 'icon_type', 'value' => array('text')),
				'group'				=> esc_html__('Main content', 'dfd-native'),
			),
			array(
				'type'				=> 'dfd_single_checkbox',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','dfd-native').'</span></span>'.esc_html__('Custom font family', 'dfd-native'),
				'param_name'		=> 'text_icon_use_google_fonts',
				'options'			=> array(
					'yes'				=> array(
						'yes'				=> esc_attr__('Yes', 'dfd-native'),
						'no'				=> esc_attr__('No', 'dfd-native'),
					),
				),
				'dependency'		=> array('element' => 'icon_type', 'value' => array('text')),
				'group'				=> esc_html__('Main content', 'dfd-native'),
			),
			array(
				'type'				=> 'google_fonts',
				'param_name'		=> 'text_icon_custom_fonts',
				'settings'			=> array(
					'fields'			=> array(
						'font_family_description' => esc_html__('Select font family.', 'dfd-native'),
						'font_style_description'  => esc_html__('Select font style.', 'dfd-native'),
					),
				),
				'dependency'		=> array('element' => 'text_icon_use_google_fonts', 'value' => 'yes'),
				'group'				=> esc_html__('Main content', 'dfd-native'),
			),
			array(
				'type'				=> 'textfield',
				'heading'			=> esc_html__('Title', 'dfd-native'),
				'param_name'		=> 'block_title',
				'value'				=> esc_html__('Call to action title', 'dfd-native'),
				'admin_label'		=> true,
				'group'				=> esc_html__('Main content', 'dfd-native'),
			),
			array(
				'type'				=> 'textfield',
				'heading'			=> esc_html__('Subtitle', 'dfd-native'),
				'param_name'		=> 'block_subtitle',
				'value'				=> esc_html__('Call to action subtitle', 'dfd-native'),
				'group'				=> esc_html__('Main content', 'dfd-native'),
			),
			array(
				'type'				=> 'dfd_single_checkbox',
				'heading'			=> '<span class="dfd-vc-toolip tooltip-bottom"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('This option allows you to add the icon to the call to action button','dfd-native').'</span></span>'.esc_html__('Icon', 'dfd-native'),
				'param_name'		=> 'show_bt_icon',
				'options'			=> array(
					'enable_bt_icon'		=> array(
						'on'				=> esc_attr__('Yes', 'dfd-native'),
						'off'				=> esc_attr__('No', 'dfd-native'),
					),
				),
				'group'				=> esc_html__('Button', 'dfd-native'),
			),
			array(
				'type'				=> 'dfd_radio_advanced',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Choose the icon library','dfd-native').'</span></span>'.esc_html__('Icon library', 'dfd-native'),
				'param_name'		=> 'select_bt_icon',
				'value'				=> 'dfd_icons',
				'options'			=> Dfd_Theme_Helpers::build_vc_icons_fonts_list(false),
				'dependency'		=> array('element' => 'show_bt_icon', 'value' => 'enable_bt_icon'),
				'group'				=> esc_html__('Button', 'dfd-native'),
			),
			array(
				'type'				=> 'iconpicker',
				'heading'			=> esc_html__('Select Icon ', 'dfd-native'),
				'param_name'		=> 'bt_dfd_icons',
				'value'				=> 'dfd-socicon-px-icon',
				'settings'			=> array(
					'emptyIcon'			=> false,
					'type'				=> 'dfd_icons',
					'iconsPerPage'		=> 4000,
				),
				'dependency'		=> array('element' => 'select_bt_icon', 'value' => 'dfd_icons'),
				'group'				=> esc_html__('Button', 'dfd-native'),
			),
			Dfd_Theme_Helpers::build_vc_icons_param('fontawesome', esc_html__('Button', 'dfd-native'), array(
				'dependency'		=> array('element' => 'select_bt_icon', 'value' => 'fontawesome'),
			), 'bt_', 'select_bt_icon'),
			Dfd_Theme_Helpers::build_vc_icons_param('openiconic', esc_html__('Button', 'dfd-native'), array(
				'dependency'		=> array('element' => 'select_bt_icon', 'value' => 'openiconic'),
			), 'bt_', 'select_bt_icon'),
			Dfd_Theme_Helpers::build_vc_icons_param('typicons', esc_html__('Button', 'dfd-native'), array(
				'dependency'		=> array('element' => 'select_bt_icon', 'value' => 'typicons'),
			), 'bt_', 'select_bt_icon'),
			Dfd_Theme_Helpers::build_vc_icons_param('entypo', esc_html__('Button', 'dfd-native'), array(
				'dependency'		=> array('element' => 'select_bt_icon', 'value' => 'entypo'),
			), 'bt_', 'select_bt_icon'),
			Dfd_Theme_Helpers::build_vc_icons_param('linecons', esc_html__('Button', 'dfd-native'), array(
				'dependency'		=> array('element' => 'select_bt_icon', 'value' => 'linecons'),
			), 'bt_', 'select_bt_icon'),
			array(
				'type'				=> 'textfield',
				'heading'			=> esc_html__('Button text', 'dfd-native'),
				'param_name'		=> 'button_text',
				'value'				=> 'Leave reply',
				'group'				=> esc_html__('Button', 'dfd-native'),
			),
			array(
				'type'				=> 'vc_link',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Allows you to add the custom link or choose the existing page','dfd-native').'</span></span>'.esc_html__('Button link', 'dfd-native'),
				'param_name'		=> 'button_link',
				'value'				=> 'url:%23|||',
				'group'				=> esc_html__('Button', 'dfd-native'),
			),
			array(
				'type'				=> 'number',
				'heading'			=> esc_html__('Blocks left/right padding', 'dfd-native'),
				'param_name'		=> 'horizontal_padding',
				'value'				=> 35,
				'edit_field_class'	=> 'vc_column vc_col-sm-6 dfd-number-wrap crum_vc no-top-padding',
				'group'				=> esc_html__('Styles', 'dfd-native'),
			),
			array(
				'type'				=> 'number',
				'heading'			=> esc_html__('Blocks top/bottom padding', 'dfd-native'),
				'param_name'		=> 'vertical_padding',
				'value'				=> 35,
				'edit_field_class'	=> 'vc_column vc_col-sm-6 dfd-number-wrap crum_vc no-top-padding',
				'group'				=> esc_html__('Styles', 'dfd-native'),
			),
			array(
				'type'				=> 'number',
				'heading'			=> esc_html__('Blocks border radius', 'dfd-native'),
				'param_name'		=> 'main_border_radius',
				'value'				=> 6,
				'edit_field_class'	=> 'vc_column vc_col-sm-6 dfd-number-wrap crum_vc',
				'group'				=> esc_html__('Styles', 'dfd-native'),
			),
			array(
				'type'				=> 'colorpicker',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Default button block background color is #f4f4f4','dfd-native').'</span></span>'.esc_html__('Button Block Background Color', 'dfd-native'),
				'class'				=> 'crum_vc',
				'param_name'		=> 'button_block_bg',
				'edit_field_class'	=> 'vc_column vc_col-sm-6 crum_vc',
				'group'				=> esc_html__('Styles', 'dfd-native'),
			),
			array(
				'type'				=> 'colorpicker',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Default main block background color is inherited from Theme Options > Styling options > Main site color','dfd-native').'</span></span>'.esc_html__('Main Block Background Color', 'dfd-native'),
				'class'				=> 'crum_vc',
				'param_name'		=> 'main_bg_color',
				'edit_field_class'	=> 'vc_column vc_col-sm-6 crum_vc no-border-bottom',
				'group'				=> esc_html__('Styles', 'dfd-native'),
			),
			array(
				'type'				=> 'dfd_heading_param',
				'text'				=> esc_html__('Button styles', 'dfd-native'),
				'param_name'		=> 'button_styles_heading',
				'class'				=> 'ult-param-heading',
				'edit_field_class'	=> 'dfd-heading-param-wrapper vc_column vc_col-sm-12',
				'group'				=> esc_html__('Styles', 'dfd-native'),
			),
			array(
				'type'				=> 'colorpicker',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Default button text color is inherited from Theme Options > General options > Default button options > Default Button Typography','dfd-native').'</span></span>'.esc_html__('Text Color', 'dfd-native'),
				'class'				=> 'crum_vc',
				'param_name'		=> 'bt_text_color',
				'edit_field_class'	=> 'vc_column vc_col-sm-6 crum_vc',
				'group'				=> esc_html__('Styles', 'dfd-native'),
			),
			array(
				'type'				=> 'colorpicker',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Default button text hover color is inherited from Theme Options > General options > Default button options > Default button hover text color','dfd-native').'</span></span>'.esc_html__('Text Hover Color', 'dfd-native'),
				'class'				=> 'crum_vc',
				'param_name'		=> 'bt_hover_text_color',
				'edit_field_class'	=> 'vc_column vc_col-sm-6 crum_vc',
				'group'				=> esc_html__('Styles', 'dfd-native'),
			),
			array(
				'type'				=> 'colorpicker',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Default background color is inherited from Theme Options > General options > Default button options > Default button background color','dfd-native').'</span></span>'.esc_html__('Background Color', 'dfd-native'),
				'class'				=> 'crum_vc',
				'param_name'		=> 'button_bg',
				'edit_field_class'	=> 'vc_column vc_col-sm-6 crum_vc',
				'group'				=> esc_html__('Styles', 'dfd-native'),
			),
			array(
				'type'				=> 'colorpicker',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Default hover background color is inherited from Theme Options > General options > Default button options > Default button hover background color','dfd-native').'</span></span>'.esc_html__('Hover Background Color', 'dfd-native'),
				'class'				=> 'crum_vc',
				'param_name'		=> 'button_hover_bg',
				'edit_field_class'	=> 'vc_column vc_col-sm-6 crum_vc',
				'group'				=> esc_html__('Styles', 'dfd-native'),
			),
			array(
				'type'				=> 'number',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('This option allows you to specify the padding left for the button, from button text to the edge of the button. The default value is 40 px, if the icon is set for the button the value is 50px','dfd-native').'</span></span>'.esc_html__('Padding left', 'dfd-native'),
				'param_name'		=> 'button_padding_left',
				'edit_field_class'	=> 'vc_column vc_col-sm-6 dfd-number-wrap crum_vc',
				'group'				=> esc_html__('Styles', 'dfd-native'),
			),
			array(
				'type'				=> 'number',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('This option allows you to specify the padding right for the button, from button text to the edge of the button. The default value is 40 px','dfd-native').'</span></span>'.esc_html__('Padding right', 'dfd-native'),
				'param_name'		=> 'button_padding_right',
				'edit_field_class'	=> 'vc_column vc_col-sm-6 dfd-number-wrap crum_vc',
				'group'				=> esc_html__('Styles', 'dfd-native'),
			),
			array(
				'type'				=> 'number',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Default border radius is inherited from Theme Options > General options > Default button options > Default button border radius','dfd-native').'</span></span>'.esc_html__('Border Radius', 'dfd-native'),
				'param_name'		=> 'button_border_radius',
				'edit_field_class'	=> 'vc_column vc_col-sm-6 dfd-number-wrap crum_vc',
				'group'				=> esc_html__('Styles', 'dfd-native'),
			),
			array(
				'type'				=> 'number',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Allows you to choose the size for the icon you have set for the button','dfd-native').'</span></span>'.esc_html__('Icon size', 'dfd-native'),
				'param_name'		=> 'bt_icon_size',
				'value'				=> 15,
				'edit_field_class'	=> 'vc_column vc_col-sm-6 dfd-number-wrap crum_vc',
				'dependency'		=> array('element' => 'show_bt_icon', 'value' => array('enable_bt_icon')),
				'group'				=> esc_html__('Styles', 'dfd-native'),
			),
			array(
				'type'				=> 'colorpicker',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Choose the color for the icon. Default icon color is #ffffff','dfd-native').'</span></span>'.esc_html__('Icon color', 'dfd-native'),
				'param_name'		=> 'bt_icon_color',
				'value'				=> '#ffffff',
				'edit_field_class'	=> 'vc_column vc_col-sm-6 crum_vc no-border-bottom',
				'dependency'		=> array('element' => 'show_bt_icon', 'value' => array('enable_bt_icon')),
				'group'				=> esc_html__('Styles', 'dfd-native'),
			),
			array(
				'type'				=> 'colorpicker',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Choose the color for the icon. Default icon hover color is #ffffff','dfd-native').'</span></span>'.esc_html__('Icon hover color', 'dfd-native'),
				'param_name'		=> 'bt_icon_hover_color',
				'value'				=> '',
				'edit_field_class'	=> 'vc_column vc_col-sm-6 crum_vc',
				'dependency'		=> array('element' => 'show_bt_icon', 'value' => array('enable_bt_icon')),
				'group'				=> esc_html__('Styles', 'dfd-native'),
			),
			array(
				'type'				=> 'dfd_heading_param',
				'text'				=> esc_html__('Title Typography', 'dfd-native'),
				'param_name'		=> 'title_t_heading',
				'class'				=> 'ult-param-heading',
				'dependency'		=> array('element' => 'block_title', 'not_empty' => true),
				'edit_field_class'	=> 'dfd-heading-param-wrapper no-top-margin vc_column vc_col-sm-12',
				'group'				=> esc_html__('Typography', 'dfd-native'),
			),
			array(
				'type'				=> 'dfd_font_container',
				'param_name'		=> 'title_font_options',
				'settings'			=> array(
					'fields'			=> array(
						'tag'				=> 'div',
						'font_size',
						'letter_spacing',
						'line_height',
						'color',
						'font_style'
					),
				),
				'dependency'		=> array('element' => 'block_title', 'not_empty' => true),
				'group'				=> esc_html__('Typography', 'dfd-native'),
			),
			array(
				'type'				=> 'dfd_single_checkbox',
				'heading'			=> '<span class="dfd-vc-toolip"><i class="dfd-socicon-question-sign"></i><span class="dfd-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','dfd-native').'</span></span>'.esc_html__('Custom font family', 'dfd-native'),
				'param_name'		=> 'use_google_fonts',
				'options'			=> array(
					'yes'				=> array(
						'yes'				=> esc_attr__('Yes', 'dfd-native'),
						'no'				=> esc_attr__('No', 'dfd-native'),
					),
				),
				'dependency'		=> array('element' => 'block_title', 'not_empty' => true),
				'group'				=> esc_html__('Typography', 'dfd-native'),
			),
			array(
				'type'				=> 'google_fonts',
				'param_name'		=> 'custom_fonts',
				'settings'			=> array(
					'fields'			=> array(
						'font_family_description' => esc_html__('Select font family.', 'dfd-native'),
						'font_style_description'  => esc_html__('Select font styling.', 'dfd-native'),
					),
				),
				'edit_field_class'	=> 'vc_column vc_col-sm-12 no-border-bottom',
				'dependency'		=> array('element' => 'use_google_fonts', 'value' => 'yes'),
				'group'				=> esc_html__('Typography', 'dfd-native'),
			),
			array(
				'type'				=> 'dfd_heading_param',
				'text'				=> esc_html__('Subtitle Typography', 'dfd-native'),
				'param_name'		=> 'subtitle_t_heading',
				'class'				=> 'ult-param-heading',
				'edit_field_class'	=> 'dfd-heading-param-wrapper vc_column vc_col-sm-12',
				'dependency'		=> array('element' => 'block_subtitle', 'not_empty' => true),
				'group'				=> esc_html__('Typography', 'dfd-native'),
			),
			array(
				'type'				=> 'dfd_font_container',
				'param_name'		=> 'subtitle_font_options',
				'settings'			=> array(
					'fields'			=> array(
						'tag'				=> 'div',
						'font_size',
						'letter_spacing',
						'line_height',
						'color',
						'font_style'
					),
				),
				'edit_field_class'	=> 'vc_column vc_col-sm-12 no-border-bottom',
				'dependency'		=> array('element' => 'block_subtitle', 'not_empty' => true),
				'group'				=> esc_html__('Typography', 'dfd-native'),
			),
			array(
				'type'				=> 'dfd_heading_param',
				'text'				=> esc_html__('Button Text Typography', 'dfd-native'),
				'param_name'		=> 'button_t_heading',
				'class'				=> 'ult-param-heading',
				'edit_field_class'	=> 'dfd-heading-param-wrapper vc_column vc_col-sm-12',
				'dependency'		=> array('element' => 'button_text', 'not_empty' => true),
				'group'				=> esc_html__('Typography', 'dfd-native'),
			),
			array(
				'type'				=> 'dfd_font_container',
				'param_name'		=> 'button_font_options',
				'settings'			=> array(
					'fields'			=> array(
						'font_size',
						'letter_spacing',
						'line_height',
						'color',
					),
				),
				'dependency'		=> array('element' => 'button_text', 'not_empty' => true),
				'group'				=> esc_html__('Typography', 'dfd-native'),
			),
		),
	)
);
