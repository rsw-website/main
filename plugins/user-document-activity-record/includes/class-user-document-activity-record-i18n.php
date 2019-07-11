<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       user-document-activity-record
 * @since      1.0.0
 *
 * @package    User_Document_Activity_Record
 * @subpackage User_Document_Activity_Record/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    User_Document_Activity_Record
 * @subpackage User_Document_Activity_Record/includes
 * @author     Mindfire Solutions <ayushs@mindfiresolutions.com>
 */
class User_Document_Activity_Record_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'user-document-activity-record',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
