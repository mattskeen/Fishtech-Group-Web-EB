<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://filipstefansson.github.io/wp-og
 * @since      0.1.0
 *
 * @package    WP_OG
 * @subpackage WP_OG/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_OG
 * @subpackage WP_OG/public
 * @author     Your Name <email@example.com>
 */
class WP_OG_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $wp_og    The ID of this plugin.
	 */
	private $wp_og;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 * @param      string    $wp_og       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $wp_og, $version ) {

		$this->wp_og = $wp_og;
		$this->version = $version;

	}

	/**
	 * Adds the OG Tags to <head>
	 *
	 * @since    0.1.0
	 */
	public function add_og_tags() {
		// If the current post type is not in users selected post types, return
		if(is_singular()) {
			$post_type = get_post_type();
			$selected_post_types = get_option('og-post_types');
			if( !in_array($post_type, $selected_post_types) )
				return;
		}

		echo $this->generate_og_tag('og:site_name', $this->get_og_site_name());
		echo $this->generate_og_tag('og:title', $this->get_og_title());
		echo $this->generate_og_tag('og:description', $this->get_og_description());
		echo $this->generate_og_tag('og:image', $this->get_og_image());
		echo $this->generate_og_tag('og:url', $this->get_og_url());

		// Only show app_id if defined
		$app_id = get_option('og-app_id');
		if( !empty($app_id) )
			echo $this->generate_og_tag('og:app_id', $app_id);
	}

	/**
	 * Returns og:title
	 *
	 * @since    0.1.0
	 * @param    object    $post     The post where the meta box is displayed. Defaults to global $post.
	 * @return   string
	 */
	function get_og_title($post = 0) {
		$og_title = '';
		if(!$post && !is_singular()) {
			$og_title = get_option('og-title');
		} else {
			$post = get_post( $post );
			$og_title = get_post_meta( $post->ID, '_og-title', true );
			$og_title = !empty($og_title) ? $og_title : get_the_title($post->ID);
		}
		return $og_title;
	}


	/**
	 * Returns og:description
	 *
	 * @since    0.1.0
	 * @param    object    $post     The post where the meta box is displayed. Defaults to global $post.
	 * @return   string
	 */
	function get_og_description($post = 0) {
		$og_description = '';
		if(!$post && !is_singular()) {
			$og_description = get_option('og-description');
		} else {
			$post = get_post( $post );
			$og_description = get_post_meta( $post->ID, '_og-description', true );
			$og_description = !empty($og_description) ? 
				$og_description : 
				apply_filters('get_the_excerpt', get_post_field('post_excerpt', $post->ID));
		}
		return $og_description;
	}


	/**
	 * Returns og:image
	 *
	 * @since    0.1.0
	 * @param    object    $post     The post where the meta box is displayed. Defaults to global $post.
	 * @return   string
	 */
	function get_og_image($post = 0) {
		$og_image = '';
		if(!$post && !is_singular()) {
			$og_image = get_option('og-image');
		} else {
			$post = get_post( $post );
			$og_image = get_post_meta( $post->ID, '_og-image', true );
			$og_image = !empty($og_image) ? $og_image : get_option('og-image');
		}
		return $og_image;
	}


	/**
	 * Returns og:site_name
	 *
	 * @since    0.1.0
	 * @return   string
	 */
	function get_og_site_name() {
		$og_site_name = get_option('og-site_name');
		$og_site_name = empty($og_site_name) ? get_bloginfo( 'name' ) : $og_site_name;
		return $og_site_name;
	}


	/**
	 * Returns og:url
	 *
	 * @since    0.1.0
	 * @return   string
	 */
	function get_og_url($post = 0) {
		$og_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		return $og_url;
	}


	/**
	 * Generates and return markup for a meta tag
	 *
	 * @since    0.1.0
	 * @param    string    $property    the property value for the <meta> tag
	 * @param    string    $content     the content value for the <meta> tag
	 * @return   string
	 */
	function generate_og_tag($property, $content) {
		if(!empty($content))
			return '<meta property="' . $property . '" content="' . $content . '"/>
		';
	}

}
