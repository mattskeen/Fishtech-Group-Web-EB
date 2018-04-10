<?php

/**
 * Fired during plugin activation
 *
 * @link       http://filipstefansson.github.io/wp-og
 * @since      0.1.0
 *
 * @package    WP_OG
 * @subpackage WP_OG/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1.0
 * @package    WP_OG
 * @subpackage WP_OG/includes
 * @author     Filip Stefansson <filip.stefansson@gmail.com>
 */
class WP_OG_Activator {

	/**
	 * Set up defaults when the plugin is activated
	 *
	 * @since    0.1.0
	 */
	public static function activate() {

        // set default site title
        $og_site_name = get_option('og-site_name');
        if( empty($og_site_name) )
            update_option('og-site_name', get_bloginfo( 'name' ));

        // set default site description
        $og_description = get_option('og-description');
        if( empty($og_description) )
            update_option('og-description', get_bloginfo( 'tagline' ));

        // set default post types
        $og_post_types = get_option('og-post_types');
        if( empty($og_post_types) ) {
            $default_post_types = ["post", "page"];
            update_option('og-post_types', $default_post_types);
        }

	}

}
