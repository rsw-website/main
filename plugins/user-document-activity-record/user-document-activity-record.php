<?php

/**
 * @wordpress-plugin
 * Plugin Name:       User Document Activity Record 2.0
 * Description:       This is to track all the document activities performed by user. It will list the document access time, withdraw time, total time for which the document is open and it will also list whether the document is marked as favourite or not.
 * Version:           1.0.0
 * Author:            Mindfire Solutions
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define( 'USER_DOCUMENT_ACTIVITY_RECORD_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-user-document-activity-record-activator.php
 */
function activate_user_document_activity_record() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-user-document-activity-record-activator.php';
	User_Document_Activity_Record_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-user-document-activity-record-deactivator.php
 */
function deactivate_user_document_activity_record() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-user-document-activity-record-deactivator.php';
	User_Document_Activity_Record_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_user_document_activity_record' );
register_deactivation_hook( __FILE__, 'deactivate_user_document_activity_record' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-user-document-activity-record.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_user_document_activity_record() {

	$plugin = new User_Document_Activity_Record();
	$plugin->run();

}
run_user_document_activity_record();
