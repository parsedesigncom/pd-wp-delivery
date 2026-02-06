<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://parsedesign.com
 * @since      1.0.0
 *
 * @package    Pd_Wp_Delivery
 * @subpackage Pd_Wp_Delivery/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Pd_Wp_Delivery
 * @subpackage Pd_Wp_Delivery/includes
 * @author     Parse Design <info@parsedesign.com>
 */
class Pd_Wp_Delivery_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'pd-wp-delivery',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
