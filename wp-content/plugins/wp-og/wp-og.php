<?php

/**
 *
 * @link              http://filipstefansson.github.io/wp-og
 * @since             0.1.1
 * @package           WP_OG
 *
 * @wordpress-plugin
 * Plugin Name:       WP-OG
 * Plugin URI:        http://filipstefansson.github.io/wp-og/wp-og-uri/
 * Description:       A WordPress plugin that let's you create and edit Open Graph Tags™ for your website.
 * Version:           0.1.2
 * Author:            Filip Stefansson
 * Author URI:        http://filipstefansson.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-og
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-og-activator.php
 */
function activate_wp_og() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-og-activator.php';
	WP_OG_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_wp_og' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-og.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_wp_og() {

	$plugin = new WP_OG();
	$plugin->run();

}
run_wp_og();
