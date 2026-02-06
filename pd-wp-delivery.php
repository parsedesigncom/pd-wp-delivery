<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://parsedesign.com
 * @since             1.0.0
 * @package           Pd_Wp_Delivery
 *
 * @wordpress-plugin
 * Plugin Name:       Delivery
 * Plugin URI:        https://parsedesign.com
 * Description:       Designed for headless WordPress setups, this plugin serves data through a REST API to support decoupled frontend environments.
 * Version:           1.0.0
 * Author:            Parse Design
 * Author URI:        https://parsedesign.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pd-wp-delivery
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PD_WP_DELIVERY_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pd-wp-delivery-activator.php
 */
function activate_pd_wp_delivery() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pd-wp-delivery-activator.php';
	Pd_Wp_Delivery_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pd-wp-delivery-deactivator.php
 */
function deactivate_pd_wp_delivery() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pd-wp-delivery-deactivator.php';
	Pd_Wp_Delivery_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pd_wp_delivery' );
register_deactivation_hook( __FILE__, 'deactivate_pd_wp_delivery' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pd-wp-delivery.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pd_wp_delivery() {

	$plugin = new Pd_Wp_Delivery();
	$plugin->run();

}
run_pd_wp_delivery();
