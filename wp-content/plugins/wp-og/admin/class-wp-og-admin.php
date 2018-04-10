<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://filipstefansson.github.io/wp-og
 * @since      0.1.0
 *
 * @package    WP_OG
 * @subpackage WP_OG/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    WP_OG
 * @subpackage WP_OG/admin
 * @author     Filip Stefansson <filip.stefansson@gmail.com>
 */
class WP_OG_Admin {

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
	 * @param    string    $wp_og       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $wp_og, $version ) {

		$this->wp_og = $wp_og;
		$this->version = $version;

	}

	/**
	 * Adds a page for the admin area, under Settings
	 *
	 * @since    0.1.0
	 */
	public function add_options_page() {
		add_options_page(
			__('Open Graph Tags', 'wp-og'), 
			__('Open Graph Tags', 'wp-og'), 
			'manage_options', 
			$this->wp_og,
			array( $this, 'load_admin_page_content' )
		);
	}


	/**
	 * Loads the view for the admin page
	 *
	 * @since    0.1.0
	 */
	public function load_admin_page_content() {
	    require_once plugin_dir_path( __FILE__ ). 'partials/wp-og-admin-display.php';
	}


	/**
	 * Registers all the options the plugin use.
	 *
	 * @since    0.1.0
	 */
	public function register_settings() {
		register_setting( 'wp-og', 'og-site_name' );
		register_setting( 'wp-og', 'og-title' );
		register_setting( 'wp-og', 'og-description' );
		register_setting( 'wp-og', 'og-app_id' );
		register_setting( 'wp-og', 'og-image' );
		register_setting( 'wp-og', 'og-post_types' );
	}


	/**
	 * Registers meta boxes for the post types the user has selected
	 *
	 * @since    0.1.0
	 */
	public function register_meta_boxes() {
		$screens = get_option('og-post_types');
		foreach ( $screens as $screen ) {
			add_meta_box(
				'wp-og',
				__('Open Graph Tags', 'wp-og'),
				array($this, 'load_meta_box_content'),
				$screen
			);
		}
	}

	/**
	 * Loads the markup for the meta box
	 *
	 * @since    0.1.0
	 * @param    object    $post    The post where the meta box is displayed. Defaults to global $post.
	 */
	public function load_meta_box_content($post) {
		require_once plugin_dir_path( __FILE__ ). 'partials/wp-og-meta-box.php';
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @since 0.1.0
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {
	
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['wp_og_inner_meta_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['wp_og_inner_meta_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'wp_og_inner_meta_box' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
        // so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		/* OK, its safe for us to save the data now. */

		// Sanitize the user input.
		$og_title = sanitize_text_field( $_POST['og-title'] );
		$og_description = sanitize_text_field( $_POST['og-description'] );
		$og_image = sanitize_text_field( $_POST['og-image'] );

		// Update the meta field.
		update_post_meta( $post_id, '_og-title', $og_title );
		update_post_meta( $post_id, '_og-description', $og_description );
		update_post_meta( $post_id, '_og-image', $og_image );

	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->wp_og, plugin_dir_url( __FILE__ ) . 'css/wp-og-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_media();
		wp_enqueue_script( $this->wp_og, plugin_dir_url( __FILE__ ) . 'js/wp-og-admin.js', array( 'jquery' ), $this->version, false );
	}

}
